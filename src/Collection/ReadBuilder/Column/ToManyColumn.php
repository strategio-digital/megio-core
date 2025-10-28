<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\Formatter\ToManyFormatter;
use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;

class ToManyColumn extends BaseColumn
{
    public function __construct(
        protected string $key,
        protected string $name,
        protected bool $visible = true,
        protected array $formatters = [],
    ) {
        $formatters[] = new ToManyFormatter();
        parent::__construct(
            key: $key,
            name: $name,
            visible: $visible,
            formatters: $formatters,
        );
    }

    public function renderer(): string
    {
        return 'join-many-column-renderer';
    }
}
