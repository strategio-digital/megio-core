<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Event;

final class CollectionEvent
{
    public const ON_PROCESSING_START = 'saas.collection.processing.start';
    
    public const ON_PROCESSING_EXCEPTION = 'saas.collection.processing.exception';
    
    public const ON_PROCESSING_FINISH = 'saas.collection.processing.finish';
}