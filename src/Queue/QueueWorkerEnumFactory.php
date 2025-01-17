<?php
declare(strict_types=1);

namespace Megio\Queue;

final readonly class QueueWorkerEnumFactory
{
    /**
     * @param class-string<IQueueWorkerEnum> $enumName
     */
    public function __construct(
        private string $enumName
    )
    {
    }
    
    public function create(string $value): IQueueWorkerEnum
    {
        return $this->enumName::from($value);
    }
}