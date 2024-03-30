<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter\Base;

use Megio\Collection\ReadBuilder\Column\Base\ShowOnlyOn;

interface IFormatter
{
    public function format(mixed $value): mixed;
    
    public function showOnlyOn(): ?ShowOnlyOn;
}