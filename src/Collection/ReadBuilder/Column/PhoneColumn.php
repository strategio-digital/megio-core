<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;

class PhoneColumn extends BaseColumn
{
    public function renderer(): string
    {
        return 'phone-column-renderer';
    }
}