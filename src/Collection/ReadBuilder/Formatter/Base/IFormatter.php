<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Formatter\Base;

interface IFormatter
{
    public function format(mixed $value): mixed;
    
    public function adminPanelOnly(): bool;
}