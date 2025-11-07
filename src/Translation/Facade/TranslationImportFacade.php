<?php

declare(strict_types=1);

namespace Megio\Translation\Facade;

use Doctrine\ORM\Exception\ORMException;
use Megio\Database\Entity\Translation\Language;
use Megio\Database\Entity\Translation\Translation;
use Megio\Database\EntityManager;
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
     * Import translations from .neon files in specified directory
     *
     * @throws Exception
     * @throws ORMException
     *
     * @return array{imported: int, updated: int, deleted: int}
     */
    public function importFromDirectory(string $directory): array
    {
        // Ensure default language exists
        $this->languageFacade->ensureDefaultLanguageExists();

        $finder = new Finder();
        $finder->files()
            ->in($directory)
            ->name('*.locale.*.neon');

        $totalImported = 0;
        $totalUpdated = 0;

        foreach ($finder as $file) {
            $result = $this->importFile($file);

            if ($result === null) {
                continue;
            }

            $totalImported += $result['imported'];
            $totalUpdated += $result['updated'];
        }

        // Mark missing translations as deleted
        $deleted = $this->markDeletedTranslations($finder);

        // Flush all changes
        $this->em->flush();

        return [
            'imported' => $totalImported,
            'updated' => $totalUpdated,
            'deleted' => $deleted,
        ];
    }

    /**
     * @throws Exception
     * @throws ORMException
     *
     * @return array{imported: int, updated: int}|null
     */
    private function importFile(SplFileInfo $file): ?array
    {
        // Extract locale from filename (e.g., user.locale.cs_CZ.neon -> cs_CZ)
        if (preg_match('/\.locale\.([a-z]{2}_[A-Z]{2})\.neon$/', $file->getFilename(), $matches) === 0) {
            return null;
        }

        $localeCode = $matches[1];

        // Extract domain from filename (e.g., user.locale.cs_CZ.neon -> user)
        $domain = str_replace('.locale.' . $localeCode . '.neon', '', $file->getFilename());

        // Find language or skip if doesn't exist
        $language = $this->em->getLanguageRepo()->findByCode($localeCode);

        if ($language === null) {
            return null;
        }

        // Parse .neon file
        $messages = $this->neonLoader->loadFromFile($file->getRealPath());

        // Import translations
        return $this->importTranslations(
            messages: $messages,
            domain: $domain,
            language: $language,
        );
    }

    /**
     * Import or update translations from messages array
     *
     * @param array<string, string> $messages Key-value pairs of translations
     *
     * @throws ORMException
     *
     * @return array{imported: int, updated: int}
     */
    private function importTranslations(
        array $messages,
        string $domain,
        Language $language,
    ): array {
        $imported = 0;
        $updated = 0;

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
                $translation->setValue($value);
                $translation->setIsDeleted(false);
                $translation->setIsFromSource(true);
                $updated++;
            }
        }

        return [
            'imported' => $imported,
            'updated' => $updated,
        ];
    }

    /**
     * @throws Exception
     */
    private function markDeletedTranslations(Finder $finder): int
    {
        $deleted = 0;
        $allTranslations = $this->em->getTranslationRepo()->findSourceTranslations();

        foreach ($allTranslations as $translation) {
            $localeCode = $translation->getLanguage()->getCode();
            $domain = $translation->getDomain();
            $key = $translation->getKey();

            // Check if this translation still exists in .neon files
            $exists = $this->translationExistsInFiles(
                finder: $finder,
                localeCode: $localeCode,
                domain: $domain,
                key: $key,
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

            $fileDomain = str_replace('.locale.' . $localeCode . '.neon', '', $file->getFilename());

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
}
