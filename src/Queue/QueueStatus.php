<?php
declare(strict_types=1);

namespace Megio\Queue;

enum QueueStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
}
