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
        $statistics = $this->languageFacade->getLanguageStatistics();

        if (count($statistics) === 0) {
            $output->writeln('<comment>No languages found</comment>');
            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders([
            'Code',
            'Name',
            'Default',
            'Enabled',
            'Total',
            'From Source',
            'From DB',
            'Deleted',
        ]);

        foreach ($statistics as $stat) {
            $table->addRow([
                $stat->code,
                $stat->name,
                $stat->isDefault ? 'Yes' : 'No',
                $stat->isEnabled ? 'Yes' : 'No',
                $stat->total,
                $stat->fromSource,
                $stat->fromDb,
                $stat->deleted,
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
