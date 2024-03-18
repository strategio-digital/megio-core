<?php
declare(strict_types=1);

namespace Megio\Collection\Builder\Rule;

use Megio\Collection\Builder\Rule\Base\BaseRule;

class MinRule extends BaseRule
{
    public function __construct(
        protected int         $min,
        protected string|null $message = null
    )
    {
    }
    
    public function name(): string
    {
        return 'min';
    }
    
    
    public function message(): string
    {
        $value = $this->field->getValue();
        
        if (is_string($value)) {
            return $this->message ?: "Field '{$this->field->getName()}' must be at least {$this->min} characters long";
        }
        
        return $this->message ?: "Field '{$this->field->getName()}' must be at least {$this->min} long";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        if (is_string($value) && mb_strlen($value) < $this->min) {
            return false;
        }
        
        if (is_numeric($value) && $value < $this->min) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Structured description for usage in front-end form
     * @return array{name: string, message: string, params: array<string,mixed>}
     */
    public function toArray(): array
    {
        $validations = parent::toArray();
        $validations['params']['min'] = $this->min;
        return $validations;
    }
}