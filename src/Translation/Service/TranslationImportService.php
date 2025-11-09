<?php

declare(strict_types=1);

namespace Megio\Translation\Service;

use Doctrine\ORM\Exception\ORMException;
use Megio\Translation\Facade\LanguageFacade;
use Megio\Translation\Facade\TranslationDeletionFacade;
use Megio\Translation\Facade\TranslationImportFacade;
use Megio\Translation\Facade\TranslationSyncFacade;
use Nette\Neon\Exception;

final readonly class TranslationImportService
{
    public function __construct(
        private LanguageFacade $languageFacade,
        private TranslationSyncFacade $translationSyncFacade,
        private TranslationImportFacade $translationImportFacade,
        private TranslationDeletionFacade $translationDeletionFacade,
    ) {}

    /**
     * Import translations from directory
     *
     * @throws Exception
     * @throws ORMException
     */
    public function importFromDirectory(string $directory): void
    {
        // 1. Synchronize default language from ENV
        $this->languageFacade->syncDefaultLanguage();

        // 2. Synchronize is_from_source for all translations
        $this->translationSyncFacade->syncFromSource();

        // 3. Import translations from .neon files
        $this->translationImportFacade->importFiles($directory);

        // 4. Synchronize is_deleted for all languages based on source .neon
        $this->translationDeletionFacade->syncDeletedFlagForAllLanguages($directory);
    }
}
