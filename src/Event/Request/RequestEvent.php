<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Event\Request;

final class RequestEvent
{
    public const BEFORE_VALIDATION = 'saas.request.before.validation';
    
    public const ON_VALIDATION_EXCEPTION = 'saas.request.on.validation.exception';
    
    public const BEFORE_PROCESSING_DATA = 'saas.request.before.process.data';
    
    public const AFTER_PROCESSING_DATA = 'saas.request.after.process.data';
}