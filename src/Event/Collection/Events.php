<?php
declare(strict_types=1);

namespace Megio\Event\Collection;

enum Events: string
{
    case ON_START = 'megio.collection.start';
    
    case ON_EXCEPTION = 'megio.collection.exception';
    
    case ON_FINISH = 'megio.collection.finish';
}