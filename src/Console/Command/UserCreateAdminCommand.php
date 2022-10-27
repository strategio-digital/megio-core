<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Console\Command;

use Framework\Database\Entity\Role\Role;
use Framework\Database\Entity\User\User;
use Framework\Database\EntityManager;
use Framework\Security\Permissions\DefaultRole;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:user:create-admin', description: 'Create a new administrator account', aliases: ['user:create-admin'])]
class UserCreateAdminCommand extends Command
{
    public function __construct(private readonly EntityManager $em)
    {
        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'E-mail');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $passwd = $input->getArgument('password');
        
        /** @var Role $role */
        $role = $this->em->getRoleRepo()->findOneBy(['name' => DefaultRole::Admin->name()]);
        $user = (new User())->setEmail($email)->setPassword($passwd)->setRole($role);
        
        $this->em->persist($user);
        $this->em->flush();
        
        $output->writeln('<info>User with \'admin\' role successfully created.</info>');
        return Command::SUCCESS;
    }
}