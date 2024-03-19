<?php
declare(strict_types=1);

namespace Megio\Collection\Builder\Rule;

use Megio\Collection\Builder\Rule\Base\BaseRule;

class MaxRule extends BaseRule
{
    public function __construct(
        protected int         $max,
        protected string|null $message = null
    )
    {
        parent::__construct($message);
    }
    
    public function name(): string
    {
        return 'max';
    }
    
    
    public function message(): string
    {
        $value = $this->field->getValue();
        
        if (is_string($value)) {
            return $this->message ?: "Field '{$this->field->getName()}' must be at most {$this->max} characters long";
        }
        
        return $this->message ?: "Field '{$this->field->getName()}' must be at most {$this->max} long";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        if (is_string($value) && mb_strlen($value) > $this->max) {
            return false;
        }
        
        if (is_numeric($value) && $value > $this->max) {
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
        $validations['params']['max'] = $this->max;
        return $validations;
    }
}