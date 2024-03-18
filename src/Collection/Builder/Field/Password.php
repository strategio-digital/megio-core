<?php
declare(strict_types=1);

namespace Megio\Collection\Builder\Field;

use Megio\Collection\Builder\Field\Base\BaseField;
use Megio\Collection\Builder\Field\Base\FieldNativeType;

class Password extends BaseField
{
    public function renderer(): string
    {
        return 'password-renderer';
    }
    
    /**
     * @param string $name
     * @param string $label
     * @param \Megio\Collection\Builder\Rule\Base\IRule[] $rules
     * @param array<string, string|int|float|bool|null> $attrs
     * @param bool $mapToEntity
     * @param \Megio\Collection\Builder\Field\Base\FieldNativeType $type
     */
    public function __construct(
        protected string          $name,
        protected string          $label,
        protected array           $rules = [],
        protected array           $attrs = [],
        protected bool            $mapToEntity = true,
        protected FieldNativeType $type = FieldNativeType::PASSWORD
    )
    {
        parent::__construct($name, $label, $rules, $attrs, $mapToEntity, $type);
    }
}