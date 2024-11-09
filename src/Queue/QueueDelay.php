<?php
declare(strict_types=1);

namespace Megio\Queue;

class QueueDelay
{
    public function __construct(
        protected \DateTime $delayUntil,
        protected string $delayReason
    )
    {
    }
    
    public function getDelayUntil(): \DateTime
    {
        return $this->delayUntil;
    }
    
    public function getDelayReason(): string
    {
        return $this->delayReason;
    }
}