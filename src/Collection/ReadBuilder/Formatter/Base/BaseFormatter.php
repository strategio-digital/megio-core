<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Formatter\Base;

use Megio\Collection\ReadBuilder\Column\Base\ShowOnlyOn;

abstract class BaseFormatter implements IFormatter
{
    public function __construct(protected ?ShowOnlyOn $showOnlyOn = null)
    {
    }
    
    public function showOnlyOn(): ?ShowOnlyOn
    {
        return $this->showOnlyOn;
    }
}