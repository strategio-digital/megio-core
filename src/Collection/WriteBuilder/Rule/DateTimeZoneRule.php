<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class DateTimeZoneRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a valid array of ISO datetime with zone name. Example: [datetime: '2024-01-01 07:00:00', zone: 'Europe/Prague']";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        if (!is_array($value)) {
            return false;
        }
        
        if (!array_key_exists('datetime', $value) || !array_key_exists('zone', $value)) {
            return false;
        }
        
        $dateTimeString = $value['datetime'];
        $zoneString = $value['zone'];
        
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1]) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $dateTimeString)) {
            return false;
        }
        
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeString);
        $errors = \DateTime::getLastErrors();
        
        if ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
            return false;
        }
        
        try {
            new \DateTimeZone($zoneString);
        } catch (\Exception) {
            return false;
        }
        
        if ($date instanceof \DateTime) {
            return true;
        }
        
        return false;
    }
}