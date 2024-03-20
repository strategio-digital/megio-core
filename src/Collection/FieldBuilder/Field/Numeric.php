<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Field;

use Megio\Collection\FieldBuilder\Field\Base\BaseField;
use Megio\Collection\FieldBuilder\Field\Base\FieldNativeType;
use Megio\Collection\FieldBuilder\Rule\NumericRule;

class Numeric extends BaseField
{
    public function renderer(): string
    {
        return 'numeric-renderer';
    }
    
    /**
     * @param string $name
     * @param string $label
     * @param \Megio\Collection\FieldBuilder\Rule\Base\IRule[] $rules
     * @param array<string, string|int|float|bool|null> $attrs
     * @param bool $mapToEntity
     * @param \Megio\Collection\FieldBuilder\Field\Base\FieldNativeType $type
     */
    public function __construct(
        protected string          $name,
        protected string          $label,
        protected array           $rules = [],
        protected array           $attrs = [],
        protected bool            $mapToEntity = true,
        protected FieldNativeType $type = FieldNativeType::NUMBER
    )
    {
        $rules[] = new NumericRule();
        parent::__construct($name, $label, $rules, $attrs, $mapToEntity, $type);
    }
}