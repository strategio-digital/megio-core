<?php
declare(strict_types=1);

namespace Megio\Console;

use App\EntityManager;
use Megio\Database\Repository\QueueRepository;
use Megio\Queue\IQueueWorker;
use Megio\Queue\IQueueWorkerEnum;
use Megio\Queue\QueueWorkerEnumFactory;
use Nette\DI\Container;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;

#[AsCommand(name: 'app:queue', description: 'Process some heavy stuff in jobs queue.')]
class QueueWorkerCommand extends Command
{
    /**
     * Maximum jobs processed during worker startup.
     * Don't worry, after reaching this number, the worker will automatically restart and continue processing.
     */
    protected int $maxJobs = 100;
    
    /**
     * Prevents high peaks in CPU usage.
     * Sleep time between each job in microseconds.
     * 1 * 1000 * 1000 = 1 second
     */
    protected int $jobSleepMicroseconds = 1 * 1000 * 1000;
    
    private readonly QueueRepository $repository;
    
    public function __construct(
        private readonly EntityManager          $em,
        private readonly Container              $container,
        private readonly QueueWorkerEnumFactory $queueWorkerEnumFactory,
    )
    {
        $this->repository = $this->em->getQueueRepo();
        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this->addArgument('workerName', InputArgument::REQUIRED, 'Queue job name');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pid = (int)getmypid();
        $name = $input->getArgument('workerName');
        $worker = $this->queueWorkerEnumFactory->create($name);
        
        $processor = $this->container->createInstance($worker->className());
        
        if (!$processor instanceof IQueueWorker) {
            throw new \RuntimeException('Queue worker must implement IQueueJob interface');
        }
        
        $this->em->getConfiguration()->setMiddlewares([]);
        
        $date = new \DateTime()->format('Y-m-d H:i:s');
        $output->writeln("[$date] | Starting loop | Worker: {$worker->value} | PID: {$pid} | Memory: {$this->getMemoryUsageMB()} MB");
        
        $iterations = 0;
        while ($iterations < $this->maxJobs) {
            $iterations++;
            $this->loopTask($pid, $processor, $worker, $output);
            gc_collect_cycles();
            usleep($this->jobSleepMicroseconds);
        }
        
        $date = (new \DateTime())->format('Y-m-d H:i:s');
        $output->writeln("[$date] | Finished loop | Worker: {$worker->value} | PID: {$pid} | Memory: {$this->getMemoryUsageMB()} MB");
        
        return Command::SUCCESS;
    }
    
    protected function loopTask(
        int              $pid,
        IQueueWorker     $processor,
        IQueueWorkerEnum $worker,
        OutputInterface  $output,
    ): void
    {
        $queue = $this->repository->fetchQueueJob($pid, $worker);
        $queueId = $queue?->getId();
        
        try {
            if ($queue) {
                // Process queue
                $date = new \DateTime()->format('Y-m-d H:i:s');
                $output->writeln("[$date] | Processing job | Worker: {$worker->value} | PID: {$pid} | Queue ID: {$queueId} | Memory: {$this->getMemoryUsageMB()} MB | retries: {$queue->getErrorRetries()}");
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
        
        if ($queue) {
            $date = new \DateTime()->format('Y-m-d H:i:s');
            $output->writeln("[$date] | Finished job | Worker {$worker->value} | PID: {$pid} | | Queue ID: {$queueId} | Memory: {$this->getMemoryUsageMB()} MB");
        }
    }
    
    protected function getMemoryUsageMB(): float
    {
        return memory_get_usage(true) / 1024 / 1024;
    }
}