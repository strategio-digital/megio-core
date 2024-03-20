<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Field\SelectField;

class Item
{
    /**
     * @param string|int|float|bool|null $value
     * @param string $label
     * @param array<string, string|int|float|bool|null> $attrs
     */
    public function __construct(
        protected string|int|float|bool|null $value,
        protected string                     $label,
        protected array                      $attrs = []
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
     * @return array<string, string|int|float|bool|null>
     */
    public function getAttrs(): array
    {
        return $this->attrs;
    }
    
    /**
     * @return array{label: string, value:string|int|float|bool|null, attrs: array<string, string|int|float|bool|null>}
     */
    public function toArray(): array
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->getValue(),
            'attrs' => $this->getAttrs()
        ];
    }
}