<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;
use Nette\Utils\Validators;

class IntegerRule extends BaseRule
{
    public function name(): string
    {
        return 'integer';
    }
    
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' must be an integer number";
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
        
        if (is_integer($value)) {
            return true;
        }
        
        return false;
    }
}