<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Formatter;

use Megio\Collection\ReadBuilder\Formatter\Base\BaseFormatter;

class DateCzFormatter extends BaseFormatter
{
    public function format(mixed $value): mixed
    {
        if ($value instanceof \DateTime || $value instanceof \DateTimeImmutable) {
            return $value->format('j.n.Y');
        }
        
        return $value;
    }
}