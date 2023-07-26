<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Extension\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Schema\SqliteSchemaManager;

class SqliteForeignKeyChecksSubscriber implements EventSubscriber
{
    /** @return string[] */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postConnect
        ];
    }
    
    public function postConnect(ConnectionEventArgs $args): void
    {
        $connection = $args->getConnection();
        
        if (!$connection->createSchemaManager() instanceof SqliteSchemaManager) {
            return;
        }
        
        $connection->executeStatement('PRAGMA foreign_keys = ON;');
    }
}