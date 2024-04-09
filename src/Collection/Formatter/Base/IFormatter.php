<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter\Base;

use Megio\Collection\ReadBuilder\Column\Base\ShowOnlyOn;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\WriteBuilder\WriteBuilder;

interface IFormatter
{
    public function setBuilder(WriteBuilder|ReadBuilder $builder): void;
    
    public function format(mixed $value, string $key): mixed;
    
    public function showOnlyOn(): ?ShowOnlyOn;
}