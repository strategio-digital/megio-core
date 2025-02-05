<?php
declare(strict_types=1);

namespace Megio\Console;

use Megio\Database\Enum\ResourceType;
use Megio\Database\Manager\AuthResourceManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:auth:resources:update', description: 'Create resources in database.', aliases: ['resources'])]
class PermissionsUpdateCommand extends Command
{
    public function __construct(private readonly AuthResourceManager $manager)
    {
        parent::__construct();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $types = array_filter(ResourceType::cases(), fn($case) => $case !== ResourceType::VUE_ROUTER);
        $result = $this->manager->updateResources(true, [], ...$types);
        
        foreach ($result['created'] as $resource) {
            $output->writeln("<info>Resource '{$resource}' created.</info>");
        }
        
        foreach ($result['removed'] as $resource) {
            $output->writeln("<comment>Resource '{$resource}' removed.</comment>");
        }
        
        $diffCount = count($result['created']) + count($result['removed']);
        
        if ($diffCount === 0) {
            $output->writeln('<comment>No resources for update.</comment>');
        } else {
            $output->writeln("<info>Updated {$diffCount} resources.</info>");
        }
        
        return Command::SUCCESS;
    }
}