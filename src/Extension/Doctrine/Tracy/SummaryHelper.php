<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Extension\Doctrine\Tracy;

use Doctrine\DBAL\Logging\DebugStack;

class SummaryHelper
{
    public function __construct(protected DebugStack $stack)
    {
    }
    
    public function getTotalTime(): float
    {
        if ($this->count() === 0) {
            return 0.0;
        }
        
        return array_reduce($this->stack->queries, fn($a, $query) => $a + $query['executionMS'], 0);
    }
    
    public function count(): int
    {
        if (!$this->stack->queries) {
            return 0;
        }
        
        return count($this->stack->queries);
    }
}