<?php

declare(strict_types=1);

namespace Megio\Translation\Console;

use Doctrine\ORM\Exception\ORMException;
use Megio\Helper\Path;
use Megio\Translation\Facade\LanguageFacade;
use Megio\Translation\Service\TranslationImportService;
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
        private readonly TranslationImportService $translationImportService,
        private readonly TranslationService $translationService,
        private readonly LanguageFacade $languageFacade,
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

        $this->translationImportService->importFromDirectory(Path::localeDir());
        $this->translationService->invalidateCache();

        $output->writeln('<info>Import completed successfully!</info>');
        $output->writeln('<info>Translation cache invalidated</info>');
        $output->writeln('');
        $output->writeln('<comment>Current Language Statistics:</comment>');
        $output->writeln('');

        $statistics = $this->languageFacade->getLanguageStatistics();

        $table = new Table($output);
        $table->setHeaders([
            '<info>Posix</info>',
            '<info>Code</info>',
            '<info>Name</info>',
            '<info>Default</info>',
            '<info>Enabled</info>',
            '<info>Total</info>',
            '<info>From Neon</info>',
            '<info>From DB</info>',
            '<info>Deleted</info>',
        ]);

        foreach ($statistics as $stat) {
            $defaultBadge = $stat->isDefault ? '<fg=green>✓</>' : '';
            $enabledBadge = $stat->isEnabled ? '<fg=green>✓</>' : '<fg=red>✗</>';
            $posixBadge = $stat->isDefault ? "<fg=green>{$stat->posix}</>" : $stat->posix;

            $table->addRow([
                $posixBadge,
                $stat->shortCode,
                $stat->name,
                $defaultBadge,
                $enabledBadge,
                $stat->total,
                $stat->fromSource,
                $stat->fromDb,
                $stat->deleted > 0 ? "<fg=red>{$stat->deleted}</>" : $stat->deleted,
            ]);
        }

        $table->render();
        $output->writeln('');

        return Command::SUCCESS;
    }
}
