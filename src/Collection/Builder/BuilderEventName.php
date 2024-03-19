<?php
declare(strict_types=1);

namespace Megio\Collection\Builder;

enum BuilderEventName
{
    case CREATE;
    case UPDATE;
}