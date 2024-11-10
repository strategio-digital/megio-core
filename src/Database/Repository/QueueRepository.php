<?php
declare(strict_types=1);

namespace Megio\Database\Repository;

use App\Database\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Megio\Database\Entity\Queue;
use Megio\Queue\IQueueWorkerEnum;
use Megio\Queue\QueueDelay;
use Megio\Queue\QueueStatus;
use Nette\InvalidArgumentException;

/**
 * @method \Megio\Database\Entity\Queue|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method \Megio\Database\Entity\Queue|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method \Megio\Database\Entity\Queue[] findAll()
 * @method \Megio\Database\Entity\Queue[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<QueueRepository>
 */
class QueueRepository extends EntityRepository
{
    protected const int MAX_ERROR_RETRIES = 5;
    protected const string RETRY_DELAY = '+30 minutes';
    
    public function __construct(protected EntityManagerInterface $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
    }
    
    public function fetchQueueJob(int $workerId, IQueueWorkerEnum $worker): ?Queue
    {
        return $this->em->wrapInTransaction(function (EntityManager $em) use ($workerId, $worker) {
            $qb = $this->createQueryBuilder('q');
            
            $qb
                ->where('q.status = :pendingStatus')
                ->andWhere('q.worker = :worker')
                ->andWhere('q.errorRetries < :errorRetries')
                ->andWhere('q.delayUntil IS NULL OR q.delayUntil < :dateTime')
                ->addOrderBy('q.createdAt', 'ASC')
                ->addOrderBy('q.priority', 'DESC')
                ->setMaxResults(1)
                ->setParameter('pendingStatus', QueueStatus::PENDING)
                ->setParameter('worker', $worker->value)
                ->setParameter('errorRetries', self::MAX_ERROR_RETRIES)
                ->setParameter('dateTime', new \DateTime());
            
            /** @var \Megio\Database\Entity\Queue|null $queue */
            $queue = $qb->getQuery()->getOneOrNullResult();
            
            if ($queue) {
                $queue->setStatus(QueueStatus::PROCESSING);
                $queue->setWorkerId($workerId);
                $queue->setUpdatedAt(new \DateTime());
                $em->persist($queue);
            }
            
            return $queue;
        });
    }
    
    public function remove(Queue $queue): void
    {
        $this->em->remove($queue);
        $this->em->flush();
    }
    
    /**
     * @param array<int|string, mixed> $payload
     */
    public function add(IQueueWorkerEnum $worker, array $payload, int $priority = 0, QueueDelay $delay = null): Queue
    {
        if (count($payload) === 0) {
            throw new InvalidArgumentException('Payload cannot be empty array');
        }
        
        $queue = new Queue();
        $queue->setWorker($worker);
        $queue->setPayload($payload);
        $queue->setPriority($priority);
        
        if ($delay) {
            $queue->setDelayUntil($delay->getDelayUntil());
            $queue->setDelayReason($delay->getDelayReason());
        }
        
        $this->em->persist($queue);
        $this->em->flush();
        
        return $queue;
    }
    
    public function delay(Queue $queue, QueueDelay $delay): Queue
    {
        $queue->setWorkerId(null);
        $queue->setErrorMessage(null);
        $queue->setUpdatedAt(new \DateTime());
        $queue->setStatus(QueueStatus::PENDING);
        $queue->setDelayReason($delay->getDelayReason());
        $queue->setDelayUntil($delay->getDelayUntil());
        
        $this->em->persist($queue);
        $this->em->flush();
        
        return $queue;
    }
    
    public function autoRetry(Queue $queue, ?string $error = null): Queue
    {
        $queue->setWorkerId(null);
        $queue->setUpdatedAt(new \DateTime());
        
        if ($queue->getErrorRetries() < self::MAX_ERROR_RETRIES) {
            $queue->setStatus(QueueStatus::PENDING);
            $queue->setErrorRetries($queue->getErrorRetries() + 1);
            $queue->setDelayUntil((new \DateTime())->modify(self::RETRY_DELAY));
        }
        
        if ($queue->getErrorRetries() === self::MAX_ERROR_RETRIES) {
            $queue->setStatus(QueueStatus::FAILED);
        }
        
        if ($error) {
            $queue->setErrorMessage($error);
        }
        
        $this->em->persist($queue);
        $this->em->flush();
        
        return $queue;
    }
}