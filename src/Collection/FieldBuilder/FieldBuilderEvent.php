<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder;

enum FieldBuilderEvent
{
    case CREATE;
    case UPDATE;
}