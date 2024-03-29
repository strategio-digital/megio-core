<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;
use Nette\Utils\Arrays;

class ArrayRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be an array. Example: [{\"a\": 1}, {\"b\": 2}]";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        if (!is_object($value) && Arrays::isList($value)) {
            return true;
        }
        
        return false;
    }
}