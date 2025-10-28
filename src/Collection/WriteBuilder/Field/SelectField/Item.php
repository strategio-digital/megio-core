<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field\SelectField;

class Item
{
    /**
     * @param array<string, bool|float|int|string|null> $attrs
     */
    public function __construct(
        protected string|int|float|bool|null $value,
        protected string                     $label,
        protected array                      $attrs = [],
    ) {}

    public function getValue(): string|int|float|bool|null
    {
        return $this->value;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return array<string, bool|float|int|string|null>
     */
    public function getAttrs(): array
    {
        return $this->attrs;
    }

    /**
     * @return array{label: string, value:bool|float|int|string|null, attrs: array<string, bool|float|int|string|null>}
     */
    public function toArray(): array
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->getValue(),
            'attrs' => $this->getAttrs(),
        ];
    }
}
