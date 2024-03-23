<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class DateRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a valid date in ISO format. Example: 2024-01-01";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        $nullable = array_filter($this->relatedRules, fn($rule) => $rule::class === NullableRule::class);
        
        if (count($nullable) !== 0 && $value === null) {
            return true;
        }
        
        if (!is_string($value)) {
            return false;
        }
        
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1])$/', $value)) {
            return false;
        }
        
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        $errors = \DateTime::getLastErrors();
        
        if ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
            return false;
        }
        
        if ($date instanceof \DateTime) {
            $date->setTime(0, 0);
            $this->field->setValue($date->format('Y-m-d H:i:s'));
            return true;
        }
        
        return false;
    }
}