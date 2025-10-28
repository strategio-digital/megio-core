<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine\Middleware;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware;

readonly class LoggingMiddleware implements Middleware
{
    public function __construct(private QueryLogger $logger)
    {
    }

    public function wrap(Driver $driver): Driver
    {
        return new LoggingDriver($driver, $this->logger);
    }
}
