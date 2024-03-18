<?php
declare(strict_types=1);

namespace Megio\Collection\Builder\Field\Select;

class Item
{
    public function __construct(
        protected string|int|float $value,
        protected string           $label
    )
    {
    }
    
    public function getValue(): string|int|float
    {
        return $this->value;
    }
    
    public function getLabel(): string
    {
        return $this->label;
    }
}