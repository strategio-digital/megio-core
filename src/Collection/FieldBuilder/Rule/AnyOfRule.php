<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Rule;

use Megio\Collection\FieldBuilder\Rule\Base\BaseRule;

class AnyOfRule extends BaseRule
{
    /**
     * AnyOfRule constructor.
     * @param array<int, string|int|float|bool|null> $keys
     * @param string|null $message
     */
    public function __construct(
        protected array       $keys,
        protected string|null $message = null
    )
    {
        parent::__construct($this->message);
    }
    
    public function name(): string
    {
        return 'anyOf';
    }
    
    
    public function message(): string
    {
        $keys = implode(', ', $this->keys);
        return $this->message ?: "Field '{$this->field->getName()}' must be any of '{$keys}'";
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
        
        if (in_array($value, $this->keys)) {
            return true;
        }
        
        return false;
    }
}