<?php
declare(strict_types=1);

namespace Megio\Collection\Builder\Field;

use Megio\Collection\Builder\Field\Base\BaseField;
use Megio\Collection\Builder\Field\Base\FieldNativeType;

class Select extends BaseField
{
    public function renderer(): string
    {
        return 'select-renderer';
    }
    
    /**
     * @param string $name
     * @param string $label
     * @param \Megio\Collection\Builder\Field\Select\Item[] $items
     * @param \Megio\Collection\Builder\Rule\Base\IRule[] $rules
     * @param array<string, string|int|float|bool|null> $attrs
     * @param bool $mapToEntity
     * @param \Megio\Collection\Builder\Field\Base\FieldNativeType $type
     */
    public function __construct(
        protected string          $name,
        protected string          $label,
        protected array           $items = [],
        protected array           $rules = [],
        protected array           $attrs = [],
        protected bool            $mapToEntity = true,
        protected FieldNativeType $type = FieldNativeType::SELECT
    )
    {
        parent::__construct($name, $label, $rules, $attrs, $mapToEntity, $type);
    }
    
    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $validations = parent::toArray();
        $validations['params']['items'] = array_map(fn($item) => $item->toArray(), $this->items);
        return $validations;
    }
}