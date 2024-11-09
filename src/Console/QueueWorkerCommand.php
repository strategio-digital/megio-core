<?php
declare(strict_types=1);

namespace Megio\Console;

use App\Database\EntityManager;
use Megio\Database\Repository\QueueRepository;
use Megio\Queue\IQueueWorker;
use Megio\Queue\QueueWorker;
use Nette\DI\Container;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;

#[AsCommand(name: 'queue', description: 'Process any queue job')]
class QueueWorkerCommand extends Command
{
    protected QueueRepository $repository;
    
    public function __construct(
        private readonly EntityManager $em,
        private readonly Container     $container
    )
    {
        $this->repository = $this->em->getQueueRepo();
        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this->addArgument('jobName', InputArgument::REQUIRED, 'Queue job name');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pid = (int)getmypid();
        $job = QueueWorker::from($input->getArgument('jobName'));
        $date = (new \DateTime())->format('Y-m-d H:i:s');
        $processor = $this->container->createInstance($job->className());
        
        if (!$processor instanceof IQueueWorker) {
            throw new \RuntimeException('Queue job must implement IQueueJob interface');
        }
        
        $output->writeln("[{$date}] | Starting queue worker | Job: {$job->value} | PID: {$pid}");
        $this->em->getConfiguration()->setSQLLogger();
        
        while (true) { // @phpstan-ignore-line
            // Connect to database and fetch queue item
            $date = (new \DateTime())->format('Y-m-d H:i:s');
            $this->em->getConnection()->connect();
            $queue = $this->repository->fetchQueueJob($pid, $job);
            
            try {
                if ($queue) {
                    // Process queue
                    $queueId = $queue->getId();
                    $output->writeln("[$date] | Processing queue | Job: {$job->value} | PID: {$pid} | Queue ID: {$queueId} | retries: {$queue->getErrorRetries()}");
                    $delay = $processor->process($queue, $output);
                    
                    if ($delay) {
                        $this->repository->delay($queue, $delay);
                    } else {
                        $this->repository->remove($queue);
                    }
                }
            } catch (\Throwable $e) {
                $this->repository->autoRetry($queue, $e->getMessage());
                Debugger::log($e, Debugger::ERROR);
            }
            
            $this->em->clear();
            $this->em->getConnection()->close();
            
            if (!$queue) {
                $output->writeln("[{$date}] | Queue is empty | PID: {$pid} | Job: {$job->value} | Sleeping for 5 seconds...");
                sleep(5);
            }
        }
    }
}