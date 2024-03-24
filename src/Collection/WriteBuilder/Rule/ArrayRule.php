<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class ArrayRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be an array";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        if (is_array($value)) {
            return true;
        }
        
        return false;
    }
}