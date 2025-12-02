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

use function preg_match;
use function str_replace;

final readonly class TranslationImportFacade
{
    public const string NEON_LOCALE_REGEX = '/\.locale\.([a-z]{2}_[A-Z]{2})\.neon$/';

    public function __construct(
        private EntityManager $em,
        private NeonTranslationLoader $neonLoader,
    ) {}

    /**
     * Import translations from .neon files in directory
     *
     * @throws Exception
     * @throws ORMException
     */
    public function importFiles(string $directory): void
    {
        $finder = new Finder()
            ->files()
            ->in($directory)
            ->name('*.locale.*.neon');

        foreach ($finder as $file) {
            $this->importFile($file);
        }

        $this->em->flush();
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    private function importFile(SplFileInfo $file): void
    {
        if (preg_match(self::NEON_LOCALE_REGEX, $file->getFilename(), $matches) === 0) {
            return;
        }

        $localePosix = $matches[1];
        $domain = $this->extractDomainFromFilename($file->getFilename(), $localePosix);

        $language = $this->em->getLanguageRepo()->findOneByPosix($localePosix);

        if ($language === null) {
            return;
        }

        $messages = $this->neonLoader->loadFromFile($file->getRealPath());

        $this->importTranslations(
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
    ): void {
        foreach ($messages as $key => $value) {
            $translation = $this->em->getTranslationRepo()->findByKeyDomainAndLanguage(
                key: $key,
                domain: $domain,
                language: $language,
            );

            if ($translation === null) {
                $this->createNewTranslation($key, $domain, $language, $value);
            } else {
                $this->updateExistingTranslation($translation, $language, $value);
            }
        }
    }

    /**
     * @throws ORMException
     */
    private function createNewTranslation(
        string $key,
        string $domain,
        Language $language,
        string $value,
    ): void {
        $translation = new Translation();
        $translation->setKey($key);
        $translation->setDomain($domain);
        $translation->setLanguage($language);
        $translation->setValue($value);
        $translation->setIsFromSource($language->isDefault());
        $this->em->persist($translation);
    }

    private function updateExistingTranslation(
        Translation $translation,
        Language $language,
        string $value,
    ): void {
        $translation->setValue($value);
        $translation->setIsDeleted(false);
        $translation->setIsFromSource($language->isDefault());
    }

    private function extractDomainFromFilename(string $filename, string $posix): string
    {
        return str_replace('.locale.' . $posix . '.neon', '', $filename);
    }
}
