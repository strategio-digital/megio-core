<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Formatter\Base;

abstract class BaseFormatter implements IFormatter
{
    public function __construct(protected bool $adminPanelOnly = false)
    {
    }
    
    public function adminPanelOnly(): bool
    {
        return $this->adminPanelOnly;
    }
}