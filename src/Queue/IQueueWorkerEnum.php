<?php
declare(strict_types=1);

namespace Megio\Queue;

use BackedEnum;

interface IQueueWorkerEnum extends BackedEnum
{
    /**
     * @return class-string<IQueueWorker>
     */
    public function className(): string;
}
