<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Transformer\Base;

abstract class BaseTransformer implements ITransformer
{
    public function __construct(protected bool $adminPanelOnly = false)
    {
    }
    
    public function adminPanelOnly(): bool
    {
        return $this->adminPanelOnly;
    }
}