<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\WriteBuilder\Field\Base\BaseField;
use Megio\Collection\WriteBuilder\Rule\DateTimeCzRule;
use Megio\Collection\WriteBuilder\Serializer\DateTimeSerializer;

class DateTimeCzField extends BaseField
{
    public function renderer(): string
    {
        return 'date-time-cz-field-renderer';
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
        protected bool   $mapToEntity = true
    )
    {
        $rules[] = new DateTimeCzRule();
        $serializers[] = new DateTimeSerializer();
        
        parent::__construct(
            name: $name,
            label: $label,
            rules: $rules,
            serializers: $serializers,
            attrs: $attrs,
            disabled: $disabled,
            mapToEntity: $mapToEntity
        );
    }
}