<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine\Middleware;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use SensitiveParameter;

class LoggingDriver extends AbstractDriverMiddleware
{
    public function __construct(
        Driver $wrappedDriver,
        private readonly QueryLogger $logger
    ) {
        parent::__construct($wrappedDriver);
    }

    public function connect(
        #[SensitiveParameter]
        array $params
    ): Connection {
        return new LoggingConnection(
            parent::connect($params),
            $this->logger
        );
    }
}
