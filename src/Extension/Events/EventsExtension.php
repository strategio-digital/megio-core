<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Extension\Events;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventsExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::arrayOf('string');
    }
    
    public function loadConfiguration(): void
    {
        /** @var string[] $classes */
        $classes = $this->config;
        $builder = $this->getContainerBuilder();
        
        $this->initialization->addBody('$dispatcher = $this->getByType(?);', [EventDispatcher::class]);
        
        foreach ($classes as $key => $className) {
            $d = $builder->addDefinition($this->prefix("event_$key"))->setType($className);
            $this->initialization->addBody('$dispatcher->addSubscriber($this->getService(?));', [$d->getName()]);
        }
    }
}