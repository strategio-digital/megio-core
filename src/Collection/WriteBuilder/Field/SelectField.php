<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\WriteBuilder\Field\Base\BaseField;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;

class SelectField extends BaseField
{
    public function renderer(): string
    {
        return 'select-field-renderer';
    }
    
    /**
     * @param \Megio\Collection\WriteBuilder\Field\SelectField\Item[] $items
     * @param \Megio\Collection\WriteBuilder\Rule\Base\IRule[] $rules
     * @param array<string, string|int|float|bool|null> $attrs
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected array  $items = [],
        protected array  $rules = [],
        protected array  $serializers = [],
        protected array  $attrs = [],
        protected bool   $disabled = false,
        protected bool   $mapToEntity = true,
        protected mixed  $defaultValue = new UndefinedValue()
    )
    {
        parent::__construct(
            name: $name,
            label: $label,
            rules: $rules,
            serializers: $serializers,
            attrs: $attrs,
            disabled: $disabled,
            mapToEntity: $mapToEntity,
            defaultValue: $defaultValue
        );
    }
    
    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['params']['items'] = array_map(fn($item) => $item->toArray(), $this->items);
        return $data;
    }
}