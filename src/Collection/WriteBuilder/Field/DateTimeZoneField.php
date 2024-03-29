<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\WriteBuilder\Field\Base\BaseField;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Rule\DateTimeRule;
use Megio\Collection\WriteBuilder\Rule\DateTimeZoneRule;
use Megio\Collection\WriteBuilder\Serializer\DateTimeSerializer;
use Megio\Collection\WriteBuilder\Serializer\DateTimeZoneSerializer;

class DateTimeZoneField extends BaseField
{
    public function renderer(): string
    {
        return 'date-time-zone-field-renderer';
    }
    
    /**
     * @param \Megio\Collection\WriteBuilder\Rule\Base\IRule[] $rules
     * @param array<string, string|int|float|bool|null> $attrs
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected array  $rules = [],
        protected array  $serializers = [],
        protected array  $attrs = [],
        protected bool   $disabled = false,
        protected bool   $mapToEntity = true,
        protected mixed  $defaultValue = new UndefinedValue()
    )
    {
        $rules[] = new DateTimeZoneRule();
        $serializers[] = new DateTimeZoneSerializer();
        
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
    
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['params']['items'] = \DateTimeZone::listIdentifiers();
        return $data;
    }
}