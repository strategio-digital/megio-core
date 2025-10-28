<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine;

use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Megio\Extension\Doctrine\Middleware\QueryLogger;
use Nette\DI\CompilerExtension;

class DoctrineExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition('doctrineQueryLogger')->setType(QueryLogger::class);

        $builder->addDefinition('doctrine')
            ->setType(Doctrine::class)
            ->setArguments(['@doctrineQueryLogger']);

        $builder->addDefinition('entityManager')
            ->setFactory('@doctrine::getEntityManager');

        $builder->addDefinition('entityManagerProvider')
            ->setFactory(SingleManagerProvider::class, ['@entityManager']);

        $builder->addDefinition('migrationFactory')
            ->setFactory('@doctrine::getMigrationFactory');

        $this->initialization->addBody('$queryLogger = $this->getService(?);', ['doctrineQueryLogger']);
        $this->initialization->addBody('\Tracy\Debugger::getBar()->addPanel(new \Megio\Extension\Doctrine\Tracy\DoctrineTracyPanel($queryLogger));');
    }
}
