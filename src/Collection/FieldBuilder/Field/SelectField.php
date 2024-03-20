<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Field;

use Megio\Collection\FieldBuilder\Field\Base\BaseField;

class SelectField extends BaseField
{
    public function renderer(): string
    {
        return 'select-renderer';
    }
    
    /**
     * @param \Megio\Collection\FieldBuilder\Field\SelectField\Item[] $items
     * @param \Megio\Collection\FieldBuilder\Rule\Base\IRule[] $rules
     * @param array<string, string|int|float|bool|null> $attrs
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected array  $items = [],
        protected array  $rules = [],
        protected array  $attrs = [],
        protected bool   $disabled = false,
        protected bool   $mapToEntity = true
    )
    {
        parent::__construct($name, $label, $rules, $attrs, $disabled, $mapToEntity);
    }
    
    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $validations = parent::toArray();
        $validations['params']['items'] = array_map(fn($item) => $item->toArray(), $this->items);
        return $validations;
    }
}