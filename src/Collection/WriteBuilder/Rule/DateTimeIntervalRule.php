<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class DateTimeIntervalRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a valid date-time interval in ISO format. Example: 2024-01-01 07:00:00 - 2024-01-01 08:00:00";
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
        
        $dateTimeRx = '\d{4}-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1]) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])';
        
        if (!preg_match("/^($dateTimeRx) \- ($dateTimeRx)$/", $value, $matches)) {
            return false;
        }
        
        $from = \DateTime::createFromFormat('Y-m-d H:i:s', $matches[1]);
        $fromErrors = \DateTime::getLastErrors();
        
        $to = \DateTime::createFromFormat('Y-m-d H:i:s', $matches[7]);
        $toErrors = \DateTime::getLastErrors();
        
        if ($fromErrors && ($fromErrors['warning_count'] > 0 || $fromErrors['error_count'] > 0)) {
            return false;
        }
        
        if ($toErrors && ($toErrors['warning_count'] > 0 || $toErrors['error_count'] > 0)) {
            return false;
        }
        
        if ($from instanceof \DateTime && $to instanceof \DateTime) {
            return true;
        }
        
        return false;
    }
}