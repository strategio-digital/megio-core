<?php

declare(strict_types=1);

namespace Megio\Translation\Facade;

use Doctrine\ORM\Exception\ORMException;
use Megio\Database\Entity\Translation\Language;
use Megio\Database\Entity\Translation\Translation;
use Megio\Database\EntityManager;
use Megio\Translation\Facade\Dto\TranslationImportStatisticsDto;
use Megio\Translation\Loader\NeonTranslationLoader;
use Nette\Neon\Exception;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_key_exists;
use function preg_match;
use function str_contains;
use function str_replace;

final readonly class TranslationImportFacade
{
    public function __construct(
        private EntityManager $em,
        private NeonTranslationLoader $neonLoader,
        private LanguageFacade $languageFacade,
    ) {}

    /**
     * @throws Exception
     * @throws ORMException
     */
    public function importFromDirectory(string $directory): TranslationImportStatisticsDto
    {
        $this->languageFacade->ensureDefaultLanguageExists();

        $finder = new Finder()
            ->files()
            ->in($directory)
            ->name('*.locale.*.neon');

        $totalImported = 0;
        $totalUpdated = 0;
        $totalUnchanged = 0;

        foreach ($finder as $file) {
            $result = $this->importFile($file);

            if ($result === null) {
                continue;
            }

            $totalImported += $result->imported;
            $totalUpdated += $result->updated;
            $totalUnchanged += $result->unchanged;
        }

        $deleted = $this->markDeletedTranslations($finder);

        $this->em->flush();

        return new TranslationImportStatisticsDto(
            imported: $totalImported,
            updated: $totalUpdated,
            unchanged: $totalUnchanged,
            deleted: $deleted,
        );
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    private function importFile(SplFileInfo $file): ?TranslationImportStatisticsDto
    {
        if (preg_match('/\.locale\.([a-z]{2}_[A-Z]{2})\.neon$/', $file->getFilename(), $matches) === 0) {
            return null;
        }

        $localeCode = $matches[1];
        $domain = $this->extractDomainFromFilename($file->getFilename(), $localeCode);

        $language = $this->em->getLanguageRepo()->findByCode($localeCode);

        if ($language === null) {
            return null;
        }

        $messages = $this->neonLoader->loadFromFile($file->getRealPath());

        return $this->importTranslations(
            messages: $messages,
            domain: $domain,
            language: $language,
        );
    }

    /**
     * @param array<string, string> $messages
     *
     * @throws ORMException
     */
    private function importTranslations(
        array $messages,
        string $domain,
        Language $language,
    ): TranslationImportStatisticsDto {
        $imported = 0;
        $updated = 0;
        $unchanged = 0;

        foreach ($messages as $key => $value) {
            $translation = $this->em->getTranslationRepo()->findByKeyDomainAndLanguage(
                key: $key,
                domain: $domain,
                language: $language,
            );

            if ($translation === null) {
                $translation = new Translation();
                $translation->setKey($key);
                $translation->setDomain($domain);
                $translation->setLanguage($language);
                $translation->setValue($value);
                $translation->setIsFromSource(true);
                $this->em->persist($translation);
                $imported++;
            } else {
                $valueChanged = $translation->getValue() !== $value;

                $translation->setValue($value);
                $translation->setIsDeleted(false);
                $translation->setIsFromSource(true);

                if ($valueChanged === true) {
                    $updated++;
                } else {
                    $unchanged++;
                }
            }
        }

        return new TranslationImportStatisticsDto(
            imported: $imported,
            updated: $updated,
            unchanged: $unchanged,
            deleted: 0,
        );
    }

    /**
     * @throws Exception
     */
    private function markDeletedTranslations(Finder $finder): int
    {
        $deleted = 0;
        $allTranslations = $this->em->getTranslationRepo()->findSourceTranslations();

        foreach ($allTranslations as $translation) {
            $exists = $this->translationExistsInFiles(
                finder: $finder,
                localeCode: $translation->getLanguage()->getCode(),
                domain: $translation->getDomain(),
                key: $translation->getKey(),
            );

            if ($exists === false) {
                $translation->setIsDeleted(true);
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * @throws Exception
     */
    private function translationExistsInFiles(
        Finder $finder,
        string $localeCode,
        string $domain,
        string $key,
    ): bool {
        foreach ($finder as $file) {
            if (str_contains($file->getFilename(), ".locale.{$localeCode}.neon") === false) {
                continue;
            }

            $fileDomain = $this->extractDomainFromFilename($file->getFilename(), $localeCode);

            if ($fileDomain !== $domain) {
                continue;
            }

            $messages = $this->neonLoader->loadFromFile($file->getRealPath());

            if (array_key_exists($key, $messages) === true) {
                return true;
            }
        }

        return false;
    }

    private function extractDomainFromFilename(string $filename, string $localeCode): string
    {
        return str_replace('.locale.' . $localeCode . '.neon', '', $filename);
    }
}
