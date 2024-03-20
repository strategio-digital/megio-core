<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class NullableRule extends BaseRule
{
    public function name(): string
    {
        return 'nullable';
    }
    
    /**
     * This method is always true. This rule exists only for detection nullability in other rules:
     * @see BooleanRule for example
     */
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' is nullable";
    }
    
    /**
     * This method is always true. This rule exists only for detection nullability in other rules:
     * @see BooleanRule for example
     * @return bool
     */
    public function validate(): bool
    {
        return true;
    }
}