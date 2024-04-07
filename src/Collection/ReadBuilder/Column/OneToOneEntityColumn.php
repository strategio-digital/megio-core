<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\Formatter\OneToOneEntityFormatter;
use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;

class OneToOneEntityColumn extends BaseColumn
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
        $formatters[] = new OneToOneEntityFormatter();
        parent::__construct(
            key: $key,
            name: $name,
            sortable: $sortable,
            visible: $visible,
            formatters: $formatters
        );
    }
}