<?php
declare(strict_types=1);

namespace Megio\Queue;

final readonly class QueueWorkerEnumFactory
{
    /**
     * @param class-string<IQueueWorkerEnum> $workerEnumClass
     */
    public function __construct(
        private string $workerEnumClass
    )
    {
    }
    
    public function create(string $value): IQueueWorkerEnum
    {
        return $this->workerEnumClass::from($value);
    }
}