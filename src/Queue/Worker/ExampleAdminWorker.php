<?php
declare(strict_types=1);

namespace Megio\Queue\Worker;

use App\Database\EntityManager;
use Megio\Database\Entity\Queue;
use Megio\Queue\IQueueWorker;
use Megio\Queue\QueueDelay;
use Symfony\Component\Console\Output\OutputInterface;

class ExampleAdminWorker implements IQueueWorker
{
    public function __construct(
        protected EntityManager $em
    )
    {
    }
    
    public function process(Queue $queueJob, OutputInterface $output): ?QueueDelay
    {
        $admin_id = $queueJob->getPayload()['admin_id'];
        $admin = $this->em->getAdminRepo()->findOneBy(['id' => $admin_id]);
        
        if (!$admin) {
            throw new \Exception('Order not found.');
        }
        
        if ($admin->getId() === 'abc') {
            return new QueueDelay(new \DateTime('+5 minutes'), 'You can reschedule this job by QueueDelay.');
        }
        
        // Do some heavy stuff here
        // ...
        // ...
        
        // Return null to indicate that the job is done
        return null;
    }
}