<?php

declare(strict_types=1);

namespace Megio\Translation\Console;

use Doctrine\ORM\Exception\ORMException;
use Megio\Helper\Path;
use Megio\Translation\Assembler\LanguageStatisticsAssembler;
use Megio\Translation\Facade\SyncDefaultLanguageFacade;
use Megio\Translation\Facade\TranslationDeletionFacade;
use Megio\Translation\Facade\TranslationImportFacade;
use Megio\Translation\Facade\TranslationLoaderFacade;
use Megio\Translation\Facade\TranslationSyncFacade;
use Nette\Neon\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'translation:import',
    description: 'Import translations from .neon files to database',
)]
class TranslationImportCommand extends Command
{
    public function __construct(
        private readonly SyncDefaultLanguageFacade $syncDefaultLanguageFacade,
        private readonly TranslationSyncFacade $translationSyncFacade,
        private readonly TranslationImportFacade $translationImportFacade,
        private readonly TranslationDeletionFacade $translationDeletionFacade,
        private readonly TranslationLoaderFacade $translationLoaderFacade,
        private readonly LanguageStatisticsAssembler $statisticsAssembler,
        private readonly LanguageStatisticsTableRenderer $tableRenderer,
    ) {
        parent::__construct();
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $output->writeln('');
        $output->writeln('<info>Starting translation import from .neon files...</info>');
        $output->writeln('');

        // 1. Synchronize default language from ENV
        $this->syncDefaultLanguageFacade->execute();

        // 2. Synchronize is_from_source for all translations
        $this->translationSyncFacade->syncFromSource();

        // 3. Import translations from .neon files
        $this->translationImportFacade->importFiles(Path::localeDir());

        // 4. Synchronize is_deleted for all languages based on source .neon
        $this->translationDeletionFacade->syncDeletedFlagForAllLanguages(Path::localeDir());

        // 5. Invalidate cache
        $this->translationLoaderFacade->invalidateCache();

        $output->writeln('<info>Import completed successfully!</info>');
        $output->writeln('<info>Translation cache invalidated</info>');
        $output->writeln('');
        $output->writeln('<comment>Current Language Statistics:</comment>');
        $output->writeln('');

        $statistics = $this->statisticsAssembler->assemble();
        $this->tableRenderer->render($output, $statistics);
        $output->writeln('');

        return Command::SUCCESS;
    }
}
