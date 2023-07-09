<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Console;

use Saas\Database\Manager\AuthResourceManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:auth:resources:update', description: 'Create resources in database', aliases: ['resources'])]
class PermissionsUpdateCommand extends Command
{
    public function __construct(private readonly AuthResourceManager $manager)
    {
        parent::__construct();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $routes = $this->manager->updateRouteResources();
        $collections = $this->manager->updateCollectionResources();
        
        $created = array_merge($routes['created'], $collections['created']);
        $removed = array_merge($routes['removed'], $collections['removed']);
        
        foreach ($created as $resource) {
            $output->writeln("<info>Resource '{$resource}' created.</info>");
        }
        
        foreach ($removed as $resource) {
            $output->writeln("<comment>Resource '{$resource}' removed.</comment>");
        }
        
        if (count($created) + count($removed) === 0) {
            $output->writeln('<comment>No resources for creation or remove.</comment>');
        }
        
        return Command::SUCCESS;
    }
}