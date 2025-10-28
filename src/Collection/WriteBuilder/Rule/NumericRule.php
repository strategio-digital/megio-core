<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class NumericRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be an integer or decimal number";
    }

    /**
     * Return true if validation is passed
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();

        if (is_float($value) || is_integer($value)) {
            return true;
        }

        return false;
    }
}
