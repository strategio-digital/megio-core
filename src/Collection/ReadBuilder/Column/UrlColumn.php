<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;

class UrlColumn extends BaseColumn
{
    public function renderer(): string
    {
        return 'url-column-renderer';
    }
}
