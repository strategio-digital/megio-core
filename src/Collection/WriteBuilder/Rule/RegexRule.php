<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

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
        parent::__construct(message: $message);
    }
    
    public function message(): string
    {
        return $this->message ?: "Field does not match the pattern '{$this->expression}'";
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
        
        if (preg_match($this->expression, $value)) {
            return true;
        }
        
        return false;
    }
}