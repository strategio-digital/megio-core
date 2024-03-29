<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class DateTimeCzRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a valid date and time in Czech format. Example: 1.1.2024 7:00:00";
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
        
        /** @var string $value */
        $value = str_replace('. ', '.', $value);
        $pattern = '/^([1-9]|1[0-9]|2[0-9]|3[0-1])\.([1-9]|1[0-2])\.([0-2]{1}[0-9]{3})\040([0-9]|1[0-9]|2[0-3])(\:(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])){2}$/';

        if (!preg_match($pattern, $value)) {
            return false;
        }
        
        $date = \DateTime::createFromFormat('j.n.Y G:i:s', $value);
        $errors = \DateTime::getLastErrors();
        
        if ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
            return false;
        }
        
        if ($date instanceof \DateTime) {
            $this->field->setValue($date->format('Y-m-d H:i:s'));
            return true;
        }
        
        return false;
    }
}