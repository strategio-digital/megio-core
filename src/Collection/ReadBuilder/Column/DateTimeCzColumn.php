<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;
use Megio\Collection\Formatter\Base\IFormatter;
use Megio\Collection\Formatter\DateTimeCzFormatter;

class DateTimeCzColumn extends BaseColumn
{
    public function renderer(): string
    {
        return 'date-time-cz-column-renderer';
    }
    
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
        $formatters[] = new DateTimeCzFormatter();
        parent::__construct(
            key: $key,
            name: $name,
            sortable: $sortable,
            visible: $visible,
            formatters: $formatters
        );
    }
}