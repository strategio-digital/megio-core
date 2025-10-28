<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine\Middleware;

class QueryLogger
{
    /** @var array<int, array{sql: string, params: array<mixed>|null, executionMS: float}> */
    public array $queries = [];

    private float $currentQueryStartTime = 0.0;

    /**
     * @param array<mixed>|null $params
     */
    public function startQuery(
        string $sql,
        ?array $params,
    ): void {
        $this->currentQueryStartTime = microtime(true);
        $this->queries[] = [
            'sql' => $sql,
            'params' => $params,
            'executionMS' => 0.0,
        ];
    }

    public function stopQuery(): void
    {
        $lastIndex = count($this->queries) - 1;
        if ($lastIndex >= 0 && $this->currentQueryStartTime > 0) {
            $this->queries[$lastIndex]['executionMS'] = microtime(true) - $this->currentQueryStartTime;
            $this->currentQueryStartTime = 0.0;
        }
    }

    public function reset(): void
    {
        $this->queries = [];
        $this->currentQueryStartTime = 0.0;
    }
}
