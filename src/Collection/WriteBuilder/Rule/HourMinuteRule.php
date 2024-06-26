<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class HourMinuteRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a valid hour and minute in ISO format. Example: 07:00";
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
        
        if (!preg_match('/^([01][0-9]|2[0-3]):([0-5][0-9])$/', $value)) {
            return false;
        }
        
        $date = \DateTime::createFromFormat('H:i', $value);
        
        if ($date instanceof \DateTime) {
            $date->setDate(1970, 1, 1);
            $date->setTime((int)$date->format('H'), (int)$date->format('i'));
            $this->field->setValue($date->format('Y-m-d H:i:s'));
            return true;
        }
        
        return false;
    }
}