<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine\Tracy;

use Megio\Extension\Doctrine\Middleware\QueryLogger;

class SummaryHelper
{
    public function __construct(protected QueryLogger $logger) {}

    public function getTotalTime(): float
    {
        if ($this->count() === 0) {
            return 0.0;
        }

        return array_reduce($this->logger->queries, fn($a, $query) => $a + $query['executionMS'], 0);
    }

    public function count(): int
    {
        if (!$this->logger->queries) {
            return 0;
        }

        return count($this->logger->queries);
    }
}
