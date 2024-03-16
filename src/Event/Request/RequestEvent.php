<?php
declare(strict_types=1);

namespace Megio\Event\Request;

final class RequestEvent
{
    public const BEFORE_VALIDATION = 'megio.request.before.validation';
    
    public const ON_VALIDATION_EXCEPTION = 'megio.request.on.validation.exception';
    
    public const BEFORE_PROCESSING_DATA = 'megio.request.before.process.data';
    
    public const AFTER_PROCESSING_DATA = 'megio.request.after.process.data';
}