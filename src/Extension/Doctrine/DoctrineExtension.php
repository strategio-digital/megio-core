<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine;

use Doctrine\DBAL\Logging\DebugStack;
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
        
        $builder->addDefinition('doctrineDebugStack')->setType(DebugStack::class);
        $this->initialization->addBody('$debugStack = $this->getService(?);', ['doctrineDebugStack']);
        $this->initialization->addBody('$configuration = $this->getService(?)->getConnection()->getConfiguration();', ['doctrine']);
        $this->initialization->addBody('$configuration->setSQLLogger($debugStack);');
        $this->initialization->addBody('\Tracy\Debugger::getBar()->addPanel(new \Megio\Extension\Doctrine\Tracy\DoctrineTracyPanel($debugStack));');
    }
}