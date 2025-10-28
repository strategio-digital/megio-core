<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine\Middleware;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Logging\Driver as LoggingDriver;
use Megio\Extension\Doctrine\Logger\SnapshotLogger;

class TracyMiddleware implements Middleware
{
    protected SnapshotLogger $logger;

    public function __construct()
    {
        $this->logger = new SnapshotLogger();
    }

    public function wrap(Driver $driver): Driver
    {
        return new LoggingDriver($driver, $this->logger);
    }

    public function getLogger(): SnapshotLogger
    {
        return $this->logger;
    }
}
