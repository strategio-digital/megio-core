<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use Megio\Collection\Formatter\Base\BaseFormatter;

class DateFormatter extends BaseFormatter
{
    public function format(mixed $value): mixed
    {
        if ($value instanceof \DateTime || $value instanceof \DateTimeImmutable) {
            return $value->format('Y-m-d');
        }
        
        return $value;
    }
}