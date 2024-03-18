<?php
declare(strict_types=1);

namespace Megio\Collection\Builder\Rule;

use Megio\Collection\Builder\Rule\Base\BaseRule;

class NullableRule extends BaseRule
{
    
    public function name(): string
    {
        return 'nullable';
    }
    
    /**
     * This method is always true. This class exists only for detection nullability in rule:
     * @see RequiredRule
     */
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' is nullable";
    }
    
    /**
     * This method is always true. This class exists only for detection nullability in rule:
     * @see RequiredRule
     * @return bool
     */
    public function validate(): bool
    {
        return true;
    }
}