<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class HourMinuteCzRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a valid hour and minute in Czech format. Example: 7:00";
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
        
        if (!preg_match('/^([0-9]|1[0-9]|2[0-3])(\:(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])){1}$/', $value)) {
            return false;
        }
        
        $date = \DateTime::createFromFormat('G:i', $value);
        
        if ($date instanceof \DateTime) {
            $date->setDate(1970, 1, 1);
            $date->setTime((int)$date->format('H'), (int)$date->format('i'));
            $this->field->setValue($date->format('Y-m-d H:i:s'));
            return true;
        }
        
        return false;
    }
}