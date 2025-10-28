<?php
declare(strict_types=1);

namespace Megio\Event\Collection;

enum EventType
{
    case READ;
    case READ_ALL;
    case CREATE;
    case UPDATE;
    case DELETE;
}
