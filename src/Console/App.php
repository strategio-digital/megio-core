<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Console;

use Saas\Extension\Doctrine\Doctrine;
use Saas\Extension\Doctrine\PostgresDefaultSchemaSubscriber;
use Nette\DI\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class App
{
    public function create(Container $container): Application
    {
        /** @var Doctrine $doctrine */
        $doctrine = $container->getByType(Doctrine::class);
        $evm = $doctrine->getEntityManager()->getEventManager();
        $evm->addEventSubscriber(new PostgresDefaultSchemaSubscriber());
        
        $app = new Application();
        $services = $container->findByType(Command::class);
        
        foreach ($services as $name) {
            /** @var Command $command */
            $command = $container->getByName($name);
            $app->add($command);
        }
        
        return $app;
    }
}