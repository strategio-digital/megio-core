<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Field;

use Megio\Collection\FieldBuilder\Field\Base\BaseField;
use Megio\Collection\FieldBuilder\Rule\SlugRule;

class SlugField extends BaseField
{
    public function renderer(): string
    {
        return 'slug-renderer';
    }
    
    /**
     * @param \Megio\Collection\FieldBuilder\Rule\Base\IRule[] $rules
     * @param array<string, string|bool|null> $attrs
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected array  $rules = [],
        protected array  $attrs = [],
        protected bool   $disabled = false,
        protected bool   $mapToEntity = true
    )
    {
        $rules[] = new SlugRule();
        parent::__construct($name, $label, $rules, $attrs, $disabled, $mapToEntity);
    }
}