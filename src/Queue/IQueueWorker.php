<?php
declare(strict_types=1);

namespace Megio\Queue;

use Megio\Database\Entity\Queue;
use Symfony\Component\Console\Output\OutputInterface;

interface IQueueWorker
{
    /**
     * Process the queue job.
     * If you want to reschedule the job, return a QueueDelay object.
     * If you want to indicate that the job is done, return null.
     *
     * @param Queue $queueJob
     * @param OutputInterface $output
     * @return QueueDelay|null
     */
    public function process(Queue $queueJob, OutputInterface $output): ?QueueDelay;
}