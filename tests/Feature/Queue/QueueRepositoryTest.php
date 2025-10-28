<?php

declare(strict_types=1);

namespace Tests\Feature\Queue;

use App\QueueWorker;
use DateTime;
use Megio\Database\Entity\Queue;
use Megio\Queue\QueueDelay;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @phpstan-type QueuePayload array<string, string|int|float|bool|null>
 */
class QueueRepositoryTest extends TestCase
{
    /**
     * @param QueuePayload $payload
     */
    #[DataProvider('queueDataProvider')]
    public function testQueueRepositoryOperations(array $payload, string $emailKey): void
    {
        // Clear queue
        $this->em->createQueryBuilder()->delete(Queue::class, 'q')->getQuery()->execute();

        // Add queue item
        $this->em->getQueueRepo()->add(QueueWorker::EXAMPLE_WORKER, $payload);

        // Fetch queue job
        $row = $this->em->getQueueRepo()->fetchQueueJob(1, QueueWorker::EXAMPLE_WORKER);

        $this->assertInstanceOf(Queue::class, $row);
        $rowPayload = $row->getPayload();
        $this->assertEquals($payload[$emailKey], $rowPayload[$emailKey]);

        // Test delay
        $datetime = new DateTime('+5 minutes');
        $delay = new QueueDelay($datetime, 'Test delay reason.');
        $this->em->getQueueRepo()->delay($row, $delay);

        $delayedRow = $this->em->getQueueRepo()->findOneBy(['id' => $row->getId()]);
        $this->assertInstanceOf(Queue::class, $delayedRow);
        $this->assertEquals($datetime->format('Y-m-d H:i:s'), $delayedRow->getDelayUntil()?->format('Y-m-d H:i:s'));

        // Test remove
        $this->em->getQueueRepo()->remove($delayedRow);
        $removedRow = $this->em->getQueueRepo()->fetchQueueJob(1, QueueWorker::EXAMPLE_WORKER);

        $this->assertNull($removedRow);
    }

    /**
     * @return array<string, array{payload: QueuePayload, emailKey: string}>
     */
    public static function queueDataProvider(): array
    {
        return [
            'queue with email payload' => [
                'payload' => [
                    'email' => 'test-' . uniqid() . '@example.com',
                ],
                'emailKey' => 'email',
            ],
        ];
    }
}
