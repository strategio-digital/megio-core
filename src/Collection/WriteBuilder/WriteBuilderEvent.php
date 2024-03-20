<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder;

enum WriteBuilderEvent
{
    case CREATE;
    case UPDATE;
}