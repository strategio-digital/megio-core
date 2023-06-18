<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Console\Command;

use Saas\Database\Entity\Role\Resource;
use Saas\Database\Entity\Role\Role;
use Saas\Database\EntityManager;
use Saas\Security\Permissions\DefaultAccess;
use Saas\Security\Permissions\DefaultResource;
use Saas\Security\Permissions\DefaultRole;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:permissions:update', description: 'Create default roles an permissions in database', aliases: ['permissions'])]
class PermissionsUpdateCommand extends Command
{
    public function __construct(private readonly EntityManager $em)
    {
        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this->addOption('force', 'f', InputArgument::REQUIRED, 'Remove all roles, resources and user permissions');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $force = $input->getOption('force');
        
        $roleRepo = $this->em->getRoleRepo();
        $resourceRepo = $this->em->getRoleResourceRepo();
        
        if ($force) {
            $resourceRepo->createQueryBuilder('R')->delete()->getQuery()->execute();
            $roleRepo->createQueryBuilder('R')->delete()->getQuery()->execute();
        }
        
        
        /** @var Role[] $roles */
        $roles = $roleRepo->findAll();
        
        /** @var Resource[] $resources */
        $resources = $resourceRepo->findAll();
        
        /** @var array<int, string> $roleNames */
        $roleNames = array_map(fn(Role $role) => $role->getName(), $roles);
        
        /** @var array<int, string> $resourceNames */
        $resourceNames = array_map(fn(Resource $resource) => $resource->getName(), $resources);
        
        // Create default roles
        foreach (DefaultRole::cases() as $role) {
            if (!in_array($role->name(), $roleNames)) {
                $row = (new Role())->setPrimary(true)->setName($role->name());
                $this->em->persist($row);
                $roles[] = $row;
            }
        }
        
        // Create default permissions
        foreach (DefaultResource::cases() as $resource) {
            if (!in_array($resource->name(), $resourceNames)) {
                $row = (new Resource())->setName($resource->name());
                $this->em->persist($row);
                $resources[] = $row;
            }
        }
        
        // Create default roles & resources by Access table
        foreach (DefaultAccess::accesses() as $roleName => $defaultResources) {
            /** @var Role $role */
            $role = current(array_filter($roles, fn(Role $role) => $role->getName() === $roleName));
            foreach ($defaultResources as $resourceName) {
                /** @var Resource $resource */
                $resource = current(array_filter($resources, fn(Resource $resource) => $resource->getName() === $resourceName));
                $role->addResource($resource);
                $this->em->persist($role);
            }
        }
        
        $this->em->flush();
        
        $output->writeln('<info>Roles & Resources successfully updated.</info>');
        return Command::SUCCESS;
    }
}