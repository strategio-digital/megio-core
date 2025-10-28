<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine\Middleware;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;

class LoggingConnection extends AbstractConnectionMiddleware
{
    public function __construct(
        Connection $wrappedConnection,
        private readonly QueryLogger $logger
    ) {
        parent::__construct($wrappedConnection);
    }

    public function prepare(string $sql): Statement
    {
        return new LoggingStatement(
            parent::prepare($sql),
            $this->logger,
            $sql
        );
    }

    public function query(string $sql): Result
    {
        $this->logger->startQuery($sql, null);
        try {
            return parent::query($sql);
        } finally {
            $this->logger->stopQuery();
        }
    }

    public function exec(string $sql): int|string
    {
        $this->logger->startQuery($sql, null);
        try {
            return parent::exec($sql);
        } finally {
            $this->logger->stopQuery();
        }
    }
}
