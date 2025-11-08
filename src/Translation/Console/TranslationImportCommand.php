<?php

declare(strict_types=1);

namespace Megio\Translation\Console;

use Doctrine\ORM\Exception\ORMException;
use Megio\Helper\Path;
use Megio\Translation\Facade\TranslationImportFacade;
use Megio\Translation\Service\TranslationService;
use Nette\Neon\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'translation:import',
    description: 'Import translations from .neon files to database',
)]
class TranslationImportCommand extends Command
{
    public function __construct(
        private readonly TranslationImportFacade $translationImportFacade,
        private readonly TranslationService $translationService,
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

        $result = $this->translationImportFacade->importFromDirectory(Path::localeDir());
        $this->translationService->invalidateCache();

        $output->writeln('<comment>Import Summary:</comment>');
        $output->writeln('');

        $table = new Table($output);
        $table->setHeaders(['<info>Status</info>', '<info>Count</info>', '<info>Description</info>']);
        $table->setRows([
            ['<fg=green>Imported</>', $result->imported, 'New translations added to database'],
            ['<fg=yellow>Updated</>', $result->updated, 'Existing translations modified'],
            ['<fg=blue>Unchanged</>', $result->unchanged, 'Translations without changes'],
            ['<fg=red>Deleted</>', $result->deleted, 'Translations marked as deleted'],
        ]);
        $table->render();

        $total = $result->imported + $result->updated + $result->unchanged;
        $output->writeln('');
        $output->writeln("<info>Total processed: {$total} translations</info>");
        $output->writeln('<info>Translation cache invalidated successfully</info>');
        $output->writeln('');

        return Command::SUCCESS;
    }
}
