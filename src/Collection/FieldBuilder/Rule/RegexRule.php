<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Rule;

use Megio\Collection\FieldBuilder\Rule\Base\BaseRule;

class RegexRule extends BaseRule
{
    /**
     * @param string $expression
     * @param string|null $message
     */
    public function __construct(
        protected string      $expression,
        protected string|null $message = null
    )
    {
        parent::__construct($message);
    }
    
    public function name(): string
    {
        return 'regex';
    }
    
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' does not match the pattern '{$this->expression}'";
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
        
        if (preg_match($this->expression, $value)) {
            return true;
        }
        
        return false;
    }
}