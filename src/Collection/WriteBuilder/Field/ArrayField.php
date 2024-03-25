<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\WriteBuilder\Field\Base\BaseField;
use Megio\Collection\WriteBuilder\Rule\ArrayRule;

class ArrayField extends BaseField
{
    public function renderer(): string
    {
        return 'array-field-renderer';
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
        $rules[] = new ArrayRule();
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