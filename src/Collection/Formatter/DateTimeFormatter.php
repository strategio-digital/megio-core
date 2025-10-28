<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use DateTime;
use DateTimeImmutable;
use Megio\Collection\Formatter\Base\BaseFormatter;

class DateTimeFormatter extends BaseFormatter
{
    public function format(mixed $value, string $key): mixed
    {
        if ($value instanceof DateTime || $value instanceof DateTimeImmutable) {
            return $value->format('Y-m-d H:i:s');
        }

        return $value;
    }
}
