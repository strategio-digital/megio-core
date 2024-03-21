<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;

class DateTimeZoneColumn extends BaseColumn
{
    public function renderer(): string
    {
        return 'date-time-zone-column-renderer';
    }
}