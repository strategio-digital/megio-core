<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\Formatter\Base\IFormatter;
use Megio\Collection\WriteBuilder\Field\Base\BaseField;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Field\SelectField\Item;
use Megio\Collection\WriteBuilder\Rule\Base\IRule;

class SelectField extends BaseField
{
    /**
     * @param Item[] $items
     * @param IRule[] $rules
     * @param IFormatter[] $formatters
     * @param array<string, bool|float|int|string|null> $attrs
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected array  $items = [],
        protected array  $rules = [],
        protected array  $serializers = [],
        protected array  $formatters = [],
        protected array  $attrs = [],
        protected bool   $disabled = false,
        protected bool   $mapToEntity = true,
        protected mixed  $value = new UndefinedValue(),
        protected mixed  $defaultValue = new UndefinedValue(),
    ) {
        parent::__construct(
            $this->name,
            $this->label,
            $this->rules,
            $this->serializers,
            $this->formatters,
            $this->attrs,
            $this->disabled,
            $this->mapToEntity,
            $this->value,
            $this->defaultValue,
        );
    }

    public function renderer(): string
    {
        return 'select-field-renderer';
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['params']['items'] = array_map(fn($item) => $item->toArray(), $this->items);
        return $data;
    }
}
