<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Event\Collection;

final class CollectionEvent
{
    public const ON_PROCESSING_START = 'megio.collection.processing.start';
    
    public const ON_PROCESSING_EXCEPTION = 'megio.collection.processing.exception';
    
    public const ON_PROCESSING_FINISH = 'megio.collection.processing.finish';
}