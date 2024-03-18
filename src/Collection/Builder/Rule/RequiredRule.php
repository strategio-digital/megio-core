<?php
declare(strict_types=1);

namespace Megio\Collection\Builder\Rule;

use Megio\Collection\Builder\Rule\Base\BaseRule;

class RequiredRule extends BaseRule
{
    public function name(): string
    {
        return 'required';
    }
    
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' is required";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        $nullable = array_filter($this->relatedRules, fn($rule) => $rule->name() === 'nullable');
        
        if (count($nullable) === 0 && $value === null) {
            return false;
        }
        
        if (is_string($value) && trim($value) === '') {
            return false;
        }
        
        return true;
    }
}