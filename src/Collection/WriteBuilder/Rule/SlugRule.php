<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class SlugRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a valid slug [a-z] [0-9] and hyphen [-] between characters.";
    }

    /**
     * Return true if validation is passed
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();

        if (!is_string($value)) {
            return false;
        }

        if (preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $value)) {
            return true;
        }

        return false;
    }
}
