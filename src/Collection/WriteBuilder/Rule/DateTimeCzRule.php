<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class DateTimeCzRule extends BaseRule
{
    public function __construct(
        protected string|null $message = null,
        protected bool        $normalize = true
    )
    {
        parent::__construct($message);
    }
    
    public function name(): string
    {
        return 'dateTimeCz';
    }
    
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' must be a valid date and time in Czech format. Example: 1.1.2024 7:00:00";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        $nullable = array_filter($this->relatedRules, fn($rule) => $rule->name() === 'nullable');
        
        if (count($nullable) !== 0 && $value === null) {
            return true;
        }
        
        if (!is_string($value)) {
            return false;
        }
        
        /** @var string $value */
        $value = str_replace('. ', '.', $value);
        $pattern = '/^([1-9]|1[0-9]|2[0-9]|3[0-1])\.([1-9]|1[0-2])\.([0-2]{1}[0-9]{3})\040([0-9]|1[0-9]|2[0-3])(\:(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])){2}$/';
        
        if (!preg_match($pattern, $value)) {
            return false;
        }
        
        $date = \DateTime::createFromFormat('d.m.Y H:i:s', $value);
        $errors = \DateTime::getLastErrors();
        
        if ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
            return false;
        }
        
        if ($date instanceof \DateTime) {
            if ($this->normalize) {
                $this->field->setValue($date->format('Y-m-d H:i:s'));
            }
            return true;
        }
        
        return false;
    }
}