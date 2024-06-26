<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class MaxRule extends BaseRule
{
    public function __construct(
        protected int     $max,
        protected ?string $message = null
    )
    {
        parent::__construct(message: $message);
    }
    
    public function message(): string
    {
        $value = $this->field->getValue();
        
        if (is_string($value)) {
            return $this->message ?: "Field must be maximum of {$this->max} characters long";
        }
        
        if (is_array($value)) {
            return $this->message ?: "Field must have maximum of {$this->max} items";
        }
        
        return $this->message ?: "Field must be equal or less than {$this->max}";
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
        
        if (is_string($value) && mb_strlen($value) <= $this->max) {
            return true;
        }
        
        if (is_array($value) && count($value) <= $this->max) {
            return true;
        }
        
        if ((is_integer($value) || is_float($value)) && $value <= $this->max) {
            return true;
        }
        
        return false;
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