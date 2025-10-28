<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use DateTime;
use DateTimeImmutable;
use Megio\Collection\Formatter\Base\BaseFormatter;

class HourMinuteFormatter extends BaseFormatter
{
    public function format(
        mixed $value,
        string $key,
    ): mixed {
        if ($value instanceof DateTime || $value instanceof DateTimeImmutable) {
            return $value->format('H:i');
        }

        return $value;
    }
}
