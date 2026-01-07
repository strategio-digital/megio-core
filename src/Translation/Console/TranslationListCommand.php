<?php

declare(strict_types=1);

namespace Megio\Translation\Console;

use Megio\Translation\Assembler\LanguageStatisticsAssembler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
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
        private readonly LanguageStatisticsAssembler $statisticsAssembler,
        private readonly LanguageStatisticsTableRenderer $tableRenderer,
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

        $statistics = $this->statisticsAssembler->assemble();

        if (count($statistics) === 0) {
            $output->writeln('<comment>No languages found</comment>');
            $output->writeln('');
            return Command::SUCCESS;
        }

        $this->tableRenderer->render($output, $statistics);
        $output->writeln('');

        return Command::SUCCESS;
    }
}
