<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\Formatter\ToOneFormatter;
use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;

class OneToOneColumn extends BaseColumn
{
    public function renderer(): string
    {
        return 'join-one-column-renderer';
    }
    
    public function __construct(
        protected string $key,
        protected string $name,
        protected bool   $sortable = false,
        protected bool   $visible = true,
        protected array  $formatters = []
    )
    {
        $formatters[] = new ToOneFormatter();
        parent::__construct(
            key: $key,
            name: $name,
            sortable: $sortable,
            visible: $visible,
            formatters: $formatters
        );
    }
}