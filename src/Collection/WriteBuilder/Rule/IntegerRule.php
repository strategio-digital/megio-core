<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class IntegerRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be an integer number";
    }

    /**
     * Return true if validation is passed
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();

        if (is_integer($value)) {
            return true;
        }

        return false;
    }
}
