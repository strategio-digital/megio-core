<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class RequiredRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field is required";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        if (is_string($value) && trim($value) !== '') {
            return true;
        }
        
        if (is_integer($value) || is_float($value)) {
            return true;
        }
        
        if (is_array($value)) {
            return true;
        }
        
        if ($value === true || $value === false) {
            return true;
        }
        
        return false;
    }
}