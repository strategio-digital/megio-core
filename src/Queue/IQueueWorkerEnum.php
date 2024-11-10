<?php
declare(strict_types=1);

namespace Megio\Queue;

interface IQueueWorkerEnum extends \BackedEnum
{
    /**
     * @return class-string<IQueueWorker>
     */
    public function className(): string;
}