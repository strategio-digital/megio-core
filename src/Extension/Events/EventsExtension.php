<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Extension\Events;

use Nette;
use Nette\DI\CompilerExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventsExtension extends CompilerExtension
{
    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Nette\Schema\Expect::arrayOf('string');
    }
    
    public function loadConfiguration(): void
    {
        /** @var string[] $serviceNames */
        $serviceNames = $this->config;
        $builder = $this->getContainerBuilder();
        
        $this->initialization->addBody('$dispatcher = $this->getByType(?);', [EventDispatcher::class]);
        
        foreach ($serviceNames as $key => $serviceName) {
            $builder->addDefinition($this->prefix("event_$key"))->setType($serviceName);
            $this->initialization->addBody('$dispatcher->addSubscriber($this->createServiceEvents__event_?());', [$key]);
            
        }
    }
}