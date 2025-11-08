<?php

declare(strict_types=1);

namespace Megio\Translation\Console;

use Megio\Translation\Facade\LanguageFacade;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function count;

#[AsCommand(
    name: 'translation:list',
    description: 'List all languages and translation statistics',
)]
class TranslationListCommand extends Command
{
    public function __construct(
        private readonly LanguageFacade $languageFacade,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $output->writeln('');
        $output->writeln('<info>Available Languages:</info>');
        $output->writeln('');

        $statistics = $this->languageFacade->getLanguageStatistics();

        if (count($statistics) === 0) {
            $output->writeln('<comment>No languages found</comment>');
            $output->writeln('');
            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders([
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
            $codeBadge = $stat->isDefault ? "<fg=green>{$stat->code}</>" : $stat->code;

            $table->addRow([
                $codeBadge,
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
