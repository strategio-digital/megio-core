<?php
declare(strict_types=1);

namespace Megio\Event\Request;

enum Events: string
{
    case BEFORE_VALIDATION = 'megio.request.before.validation';

    case ON_VALIDATION_EXCEPTION = 'megio.request.on.validation.exception';

    case BEFORE_PROCESSING_DATA = 'megio.request.before.process.data';

    case AFTER_PROCESSING_DATA = 'megio.request.after.process.data';
}
