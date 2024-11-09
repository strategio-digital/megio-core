<?php
declare(strict_types=1);

namespace Megio\Queue;

use Megio\Queue\Worker\ExampleAdminWorker;

enum QueueWorker: string
{
    case EXAMPLE_ADMIN_WORKER = 'example.admin.worker';
    
    public function className(): string
    {
        return match ($this) {
            self::EXAMPLE_ADMIN_WORKER => ExampleAdminWorker::class,
        };
    }
}