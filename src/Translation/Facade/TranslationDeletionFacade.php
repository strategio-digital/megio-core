<?php

declare(strict_types=1);

namespace Megio\Translation\Facade;

use Megio\Database\EntityManager;
use Megio\Translation\Helper\NeonFilenameHelper;
use Megio\Translation\Loader\NeonTranslationLoader;
use Nette\Neon\Exception;
use Symfony\Component\Finder\Finder;

use function in_array;
use function preg_match;

final readonly class TranslationDeletionFacade
{
    public function __construct(
        private EntityManager $em,
        private NeonTranslationLoader $neonLoader,
    ) {}

    /**
     * Synchronize is_deleted flag for ALL languages based on source .neon files
     *
     * If key exists in source .neon → all language variants get is_deleted=false
     * If key doesn't exist in source .neon → all language variants get is_deleted=true
     *
     * @throws Exception
     */
    public function syncDeletedFlagForAllLanguages(string $directory): void
    {
        $finder = new Finder()
            ->files()
            ->in($directory)
            ->name('*.locale.*.neon');

        // Collect all keys from source .neon files
        $activeKeys = $this->collectActiveKeysFromSource($finder);

        // Get ALL translations (not just source)
        $allTranslations = $this->em->getTranslationRepo()->findAll();

        foreach ($allTranslations as $translation) {
            $keyDomain = $translation->getDomain() . '.' . $translation->getKey();
            $shouldBeActive = in_array($keyDomain, $activeKeys, true);

            if ($shouldBeActive && $translation->isDeleted() === true) {
                // Key exists in source .neon → restore
                $translation->setIsDeleted(false);
            } elseif ($shouldBeActive === false && $translation->isDeleted() === false) {
                // Key doesn't exist in source .neon → mark as deleted
                $translation->setIsDeleted(true);
            }
        }

        $this->em->flush();
    }

    /**
     * Collect all unique keys+domains from DEFAULT LANGUAGE .neon files only
     *
     * @throws Exception
     *
     * @return array<string>
     */
    private function collectActiveKeysFromSource(Finder $finder): array
    {
        // Get default language from DB
        $defaultLanguage = $this->em->getLanguageRepo()->findDefault();

        if ($defaultLanguage === null) {
            return []; // No default language = no active keys
        }

        $defaultPosix = $defaultLanguage->getPosix();
        $activeKeys = [];

        foreach ($finder as $file) {
            if (preg_match(NeonFilenameHelper::LOCALE_REGEX, $file->getFilename(), $matches) === 0) {
                continue;
            }

            $posix = $matches[1];

            // FILTER: Process ONLY default language .neon files
            if ($posix !== $defaultPosix) {
                continue; // Skip en_US.neon if default is cs_CZ
            }

            $domain = NeonFilenameHelper::extractDomain($file->getFilename(), $posix);
            $messages = $this->neonLoader->loadFromFile($file->getRealPath());

            foreach ($messages as $key => $value) {
                $keyDomain = $domain . '.' . $key;
                if (in_array($keyDomain, $activeKeys, true) === false) {
                    $activeKeys[] = $keyDomain;
                }
            }
        }

        return $activeKeys;
    }
}
