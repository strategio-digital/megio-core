<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;
use Megio\Collection\ReadBuilder\Formatter\Base\IFormatter;
use Megio\Collection\ReadBuilder\Formatter\DateFormatter;

class DateColumn extends BaseColumn
{
    /**
     * @param IFormatter[] $formatters
     */
    public function __construct(
        protected string $key,
        protected string $name,
        protected bool   $sortable = false,
        protected bool   $visible = true,
        protected array  $formatters = []
    )
    {
        $formatters[] = new DateFormatter();
        parent::__construct(
            key: $key,
            name: $name,
            sortable: $sortable,
            visible: $visible,
            formatters: $formatters
        );
    }
    
    public function renderer(): string
    {
        return 'date-column-renderer';
    }
}