<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;
use Nette\Utils\Validators;

class StringRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a string";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        if (is_string($value)) {
            return true;
        }
        
        return false;
    }
}