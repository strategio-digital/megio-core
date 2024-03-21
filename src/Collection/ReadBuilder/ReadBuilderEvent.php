<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder;

enum ReadBuilderEvent
{
    case READ_ONE;
    case READ_ALL;
}