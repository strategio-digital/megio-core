<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Field;

use Megio\Collection\FieldBuilder\Field\Base\BaseField;
use Megio\Collection\FieldBuilder\Rule\ArrayRule;

class ArrayField extends BaseField
{
    public function renderer(): string
    {
        return 'array-renderer';
    }
    
    /**
     * @param string $name
     * @param string $label
     * @param \Megio\Collection\FieldBuilder\Rule\Base\IRule[] $rules
     * @param array<string, string|int|float|bool|null> $attrs
     * @param bool $mapToEntity
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected array  $rules = [],
        protected array  $attrs = [],
        protected bool   $mapToEntity = true
    )
    {
        $rules[] = new ArrayRule();
        parent::__construct($name, $label, $rules, $attrs, $mapToEntity);
    }
}