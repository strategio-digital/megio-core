<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Rule;

use Megio\Collection\FieldBuilder\Rule\Base\BaseRule;

class DecimalRule extends BaseRule
{
    public function name(): string
    {
        return 'decimal';
    }
    
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' must be a decimal number";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        $nullable = array_filter($this->relatedRules, fn($rule) => $rule->name() === 'nullable');
        
        if (count($nullable) !== 0 && $value === null) {
            return true;
        }
        
        if (is_float($value)) {
            return true;
        }
        
        return false;
    }
}