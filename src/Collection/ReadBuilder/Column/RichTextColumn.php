<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;

class RichTextColumn extends BaseColumn
{
    public function renderer(): string
    {
        return 'rich-text-column-renderer';
    }
}