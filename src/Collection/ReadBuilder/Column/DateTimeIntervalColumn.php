<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;

class DateTimeIntervalColumn extends BaseColumn
{
    public function renderer(): string
    {
        return 'date-time-interval-column-renderer';
    }
}