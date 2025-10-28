<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\Formatter\Base\IFormatter;
use Megio\Collection\Formatter\DateCzFormatter;
use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;

class DateCzColumn extends BaseColumn
{
    /**
     * @param IFormatter[] $formatters
     */
    public function __construct(
        protected string $key,
        protected string $name,
        protected bool $sortable = false,
        protected bool $visible = true,
        protected array $formatters = [],
    ) {
        $formatters[] = new DateCzFormatter();
        parent::__construct(
            key: $key,
            name: $name,
            sortable: $sortable,
            visible: $visible,
            formatters: $formatters,
        );
    }

    public function renderer(): string
    {
        return 'date-cz-column-renderer';
    }
}
