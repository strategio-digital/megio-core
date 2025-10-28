<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use DateTime;
use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class DateTimeRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a valid date and time in ISO format. Example: 2024-01-01 07:00:00";
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

        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])-([0-2][0-9]|3[0-1]) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $value)) {
            return false;
        }

        $date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
        $errors = DateTime::getLastErrors();

        if ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
            return false;
        }

        if ($date instanceof DateTime) {
            return true;
        }

        return false;
    }
}
