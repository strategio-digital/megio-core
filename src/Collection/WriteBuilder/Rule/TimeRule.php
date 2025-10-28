<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use DateTime;
use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class TimeRule extends BaseRule
{
    public function message(): string
    {
        return $this->message ?: "Field must be a valid time in ISO. Example: 07:00:00";
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

        if (!preg_match('/^([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $value)) {
            return false;
        }

        $date = DateTime::createFromFormat('H:i:s', $value);

        if ($date instanceof DateTime) {
            $date->setDate(1970, 1, 1);
            $this->field->setValue($date->format('Y-m-d H:i:s'));
            return true;
        }

        return false;
    }
}
