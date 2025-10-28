<?php declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\Formatter\CallableFormatter;
use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;

class EnumColumn extends BaseColumn
{
    public function __construct(
        protected string $key,
        protected string $name,
        protected bool $sortable = false,
        protected bool $visible = true,
    ) {
        parent::__construct(
            key: $key,
            name: $name,
            sortable: $sortable,
            visible: $visible,
            formatters: [
                new CallableFormatter(fn(
                    $value,
                ) => $value ? $value->value : null),
            ],
        );
    }

    public function renderer(): string
    {
        return 'string-column-renderer';
    }
}
