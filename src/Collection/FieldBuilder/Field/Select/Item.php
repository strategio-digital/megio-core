<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Field\Select;

class Item
{
    public function __construct(
        protected string|int|float|bool|null $value,
        protected string                     $label
    )
    {
    }
    
    public function getValue(): string|int|float|bool|null
    {
        return $this->value;
    }
    
    public function getLabel(): string
    {
        return $this->label;
    }
    
    /**
     * @return array{label: string, value:string|int|float|bool|null}
     */
    public function toArray(): array
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->getValue()
        ];
    }
}