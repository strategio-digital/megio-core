<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use DateTime;
use DateTimeImmutable;
use Megio\Collection\Formatter\Base\BaseFormatter;

class DateTimeCzFormatter extends BaseFormatter
{
    public function format(mixed $value, string $key): mixed
    {
        if ($value instanceof DateTime || $value instanceof DateTimeImmutable) {
            return $value->format('j.n.Y G:i:s');
        }

        return $value;
    }
}
