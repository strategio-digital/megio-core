<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Extension\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

final class PostgresDefaultSchemaSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            ToolEvents::postGenerateSchema
        ];
    }
    
    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schemaManager = $args
            ->getEntityManager()
            ->getConnection()
            ->createSchemaManager();
        
        if (!$schemaManager instanceof PostgreSQLSchemaManager) {
            return;
        }
        
        $schema = $args->getSchema();
        
        foreach ($schemaManager->getExistingSchemaSearchPaths() as $namespace) {
            if (!$schema->hasNamespace($namespace)) {
                $schema->createNamespace($namespace);
            }
        }
    }
}
