<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use Megio\Collection\Formatter\Base\BaseFormatter;

class DateTimeZoneCzFormatter extends BaseFormatter
{
    public function format(mixed $value, string $key): mixed
    {
        if ($value instanceof \DateTime || $value instanceof \DateTimeImmutable) {
            return [
                'value' => $value->format('j.n.Y H:i:s'),
                'iso_8601' => $value->format('c'), // '2021-08-26T14:00:00+02:00
                'zone_id' => $value->format('e'), // 'Europe/Prague'
                'utc_offset' => $value->format('P'), // '+02:00'
            ];
        }
        
        return $value;
    }
}