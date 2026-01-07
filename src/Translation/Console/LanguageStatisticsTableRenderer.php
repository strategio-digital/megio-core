<?php

declare(strict_types=1);

namespace Megio\Translation\Console;

use Megio\Translation\Facade\Dto\LanguageStatisticsDto;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

final class LanguageStatisticsTableRenderer
{
    /**
     * @param LanguageStatisticsDto[] $statistics
     */
    public function render(OutputInterface $output, array $statistics): void
    {
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
    }
}
