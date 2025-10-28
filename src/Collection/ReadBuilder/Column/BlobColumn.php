<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

class BlobColumn extends StringColumn
{
    public function renderer(): string
    {
        return 'blob-column-renderer';
    }
}
