<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Rule;

use Megio\Collection\FieldBuilder\Rule\Base\BaseRule;

class TimeCzRule extends BaseRule
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
        return 'timeCz';
    }
    
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' must be a valid time in Czech format. Example: 7:00:00";
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
        
        if (!preg_match('/^([0-9]|1[0-9]|2[0-3])(\:(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])){2}$/', $value)) {
            return false;
        }
        
        $date = \DateTime::createFromFormat('H:i:s', $value);
        
        if ($date instanceof \DateTime) {
            if ($this->normalize) {
                $date->setDate(1970, 1, 1);
                $this->field->setValue($date->format('Y-m-d H:i:s'));
            }
            return true;
        }
        
        return false;
    }
}