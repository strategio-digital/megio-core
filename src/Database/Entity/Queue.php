<?php
declare(strict_types=1);

namespace Megio\Database\Entity;

use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Field\TUpdatedAt;
use Megio\Database\Interface\ICrudable;
use Megio\Database\Repository\QueueRepository;
use Megio\Queue\IQueueWorkerEnum;
use Megio\Queue\QueueStatus;

#[ORM\Table(name: '`queue`')]
#[ORM\Entity(repositoryClass: QueueRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(fields: ['worker', 'priority'])]
class Queue implements ICrudable
{
    use TId, TCreatedAt, TUpdatedAt;
    
    #[ORM\Column]
    protected string $worker;
    
    #[ORM\Column(options: ['default' => 0])]
    protected int $priority = 0;
    
    #[ORM\Column(type: 'string', enumType: QueueStatus::class)]
    protected QueueStatus $status = QueueStatus::PENDING;
    
    /** @var array<int|string, mixed> */
    #[ORM\Column(type: 'json')]
    protected array $payload;
    
    #[ORM\Column(nullable: true)]
    protected ?\DateTime $delayUntil = null;
    
    #[ORM\Column(nullable: true)]
    protected ?string $delayReason = null;
    
    #[ORM\Column(nullable: true)]
    protected ?int $workerId = null;
    
    #[ORM\Column(options: ['default' => 0])]
    protected int $errorRetries = 0;
    
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $errorMessage = null;
    
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
    
    public function setStatus(QueueStatus $status): void
    {
        $this->status = $status;
    }
    
    public function setWorker(IQueueWorkerEnum $worker): void
    {
        $this->worker = (string)$worker->value;
    }
    
    /** @param array<int|string, mixed> $payload */
    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }
    
    public function setDelayUntil(?\DateTime $delayUntil): void
    {
        $this->delayUntil = $delayUntil;
    }
    
    public function setDelayReason(?string $delayReason): void
    {
        $this->delayReason = $delayReason;
    }
    
    public function setErrorRetries(int $errorRetries): void
    {
        $this->errorRetries = $errorRetries;
    }
    
    public function setWorkerId(?int $workerId): void
    {
        $this->workerId = $workerId;
    }
    
    public function setErrorMessage(?string $errorMessage): Queue
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }
    
    public function getPriority(): int
    {
        return $this->priority;
    }
    
    public function getErrorRetries(): int
    {
        return $this->errorRetries;
    }
    
    /** @return array<int|string, mixed> */
    public function getPayload(): array
    {
        return $this->payload;
    }
    
    public function getStatus(): QueueStatus
    {
        return $this->status;
    }
    
    public function getWorkerId(): ?int
    {
        return $this->workerId;
    }
    
    public function getDelayUntil(): ?\DateTime
    {
        return $this->delayUntil;
    }
    
    public function getDelayReason(): ?string
    {
        return $this->delayReason;
    }
    
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}