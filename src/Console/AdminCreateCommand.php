<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Console;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Saas\Database\Entity\Admin;
use Saas\Database\Entity\EntityException;
use Saas\Database\EntityManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:user:create-admin', description: 'Create a new administrator account', aliases: ['admin'])]
class AdminCreateCommand extends Command
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
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $passwd = $input->getArgument('password');
        
        try {
            $admin = new Admin();
            $admin->setEmail($email);
            $admin->setPassword($passwd);
            $this->em->persist($admin);
            $this->em->flush();
            $output->writeln('<info>Admin successfully created.</info>');
        } catch (EntityException|UniqueConstraintViolationException $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
        
        return Command::SUCCESS;
    }
}