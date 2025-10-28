<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine\Middleware;

use Doctrine\DBAL\Driver\Middleware\AbstractStatementMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;

class LoggingStatement extends AbstractStatementMiddleware
{
    /** @var array<int|string, mixed> */
    private array $params = [];

    public function __construct(
        Statement $wrappedStatement,
        private readonly QueryLogger $logger,
        private readonly string $sql
    ) {
        parent::__construct($wrappedStatement);
    }

    public function bindValue(int|string $param, mixed $value, ParameterType $type = ParameterType::STRING): void
    {
        $this->params[$param] = $value;
        parent::bindValue($param, $value, $type);
    }

    public function execute(): Result
    {
        $this->logger->startQuery($this->sql, $this->params);
        try {
            return parent::execute();
        } finally {
            $this->logger->stopQuery();
        }
    }
}
