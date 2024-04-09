<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter\Base;

use Megio\Collection\ReadBuilder\Column\Base\ShowOnlyOn;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\WriteBuilder\WriteBuilder;

abstract class BaseFormatter implements IFormatter
{
    protected WriteBuilder|ReadBuilder $builder;
    
    public function setBuilder(WriteBuilder|ReadBuilder $builder): void
    {
        $this->builder = $builder;
    }
    
    public function __construct(protected ?ShowOnlyOn $showOnlyOn = null)
    {
    }
    
    public function showOnlyOn(): ?ShowOnlyOn
    {
        return $this->showOnlyOn;
    }
}