<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Transformer\Base;

interface ITransformer
{
    public function name(): string;
    
    public function transform(mixed $value): mixed;
    
    public function adminPanelOnly(): bool;
}