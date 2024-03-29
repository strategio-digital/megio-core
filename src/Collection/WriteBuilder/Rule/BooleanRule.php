<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class BooleanRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a boolean";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        if ($value === true || $value === false) {
            return true;
        }
        
        return false;
    }
}