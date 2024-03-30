<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use Megio\Collection\Formatter\Base\BaseFormatter;

class DateTimeIntervalFormatter extends BaseFormatter
{
    public function format(mixed $value): mixed
    {
        if ($value instanceof \DateInterval) {
            return [
                'is_positive' => $value->invert === 0,
                'all_days' => $value->days,
                'each' => [
                    'years' => $value->y,
                    'months' => $value->m,
                    'days' => $value->d,
                    'hours' => $value->h,
                    'minutes' => $value->i,
                    'seconds' => $value->s,
                ]
            ];
        }
        
        return $value;
    }
}