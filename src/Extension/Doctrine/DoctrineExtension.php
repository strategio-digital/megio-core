<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Extension\Doctrine;

use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Nette\DI\CompilerExtension;

class DoctrineExtension extends CompilerExtension
{
    public function loadConfiguration() : void
    {
        $builder = $this->getContainerBuilder();
        
        $builder->addDefinition('doctrine')->setType(Doctrine::class);
        
        $builder->addDefinition('entityManagerProvider')
            ->setFactory(SingleManagerProvider::class, ['@entityManager']);
        
        $builder->addDefinition('migrationFactory')
            ->setFactory('@doctrine::getMigrationFactory');
        
        $this->initialization->addBody('$evm = $this->getService(?)->getEventManager();', ['entityManager']);
        $this->initialization->addBody('$evm->addEventSubscriber(new \Saas\Extension\Doctrine\PostgresDefaultSchemaSubscriber());');
    }
}