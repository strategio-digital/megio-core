<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Rule;

use Megio\Collection\FieldBuilder\Rule\Base\BaseRule;
use Nette\Utils\Validators;

class EmailRule extends BaseRule
{
    public function name(): string
    {
        return 'email';
    }
    
    
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' must be a valid email address";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        if (!is_string($value)) {
            return false;
        }
        
        if ($value === '') {
            return true;
        }
        
        return Validators::isEmail($value);
    }
}