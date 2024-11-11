<?php
declare(strict_types=1);

namespace Tests\Queue;

use App\Worker\QueueWorker;
use Megio\Database\Entity\Queue;
use Megio\Queue\QueueDelay;

test('queue repository tests', function () {
    $email = $this->generator()->email();
    
    $this->em()->createQueryBuilder()->delete(Queue::class, 'q')->getQuery()->execute();
    
    $this->em()->getQueueRepo()->add(QueueWorker::EXAMPLE_WORKER, [
        'email' => $email
    ]);
    
    $row = $this->em()->getQueueRepo()->fetchQueueJob(1, QueueWorker::EXAMPLE_WORKER);
    
    expect($row)
        ->toBeInstanceOf(Queue::class)
        ->and($row->getPayload())
        ->toBeArray()
        ->and($row->getPayload()['email'])
        ->toBe($email);
    
    $datetime = new \DateTime('+5 minutes');
    $delay = new QueueDelay($datetime, 'Test delay reason.');
    $this->em()->getQueueRepo()->delay($row, $delay);
    $row = $this->em()->getQueueRepo()->findOneBy(['id' => $row->getId()]);
    
    expect($row)
        ->toBeInstanceOf(Queue::class)
        ->and($row->getDelayUntil())
        ->toEqual($datetime);
    
    $this->em()->getQueueRepo()->remove($row);
    $row = $this->em()->getQueueRepo()->fetchQueueJob(1, QueueWorker::EXAMPLE_WORKER);
    
    expect($row)->toBeNull();
});