<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Field;

use Megio\Collection\WriteBuilder\Field\Base\BaseField;
use Megio\Collection\WriteBuilder\Rule\HourMinuteCzRule;

class HourMinuteCzField extends BaseField
{
    public function renderer(): string
    {
        return 'hour-minute-cz-field-renderer';
    }
    
    /**
     * @param \Megio\Collection\WriteBuilder\Rule\Base\IRule[] $rules
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
        $rules[] = new HourMinuteCzRule();
        parent::__construct($name, $label, $rules, $attrs, $disabled, $mapToEntity);
    }
}