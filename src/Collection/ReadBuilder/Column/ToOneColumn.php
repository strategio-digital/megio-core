<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Column;

use Megio\Collection\Formatter\ToOneFormatter;
use Megio\Collection\ReadBuilder\Column\Base\BaseColumn;

class ToOneColumn extends BaseColumn
{
    public function __construct(
        protected string $key,
        protected string $name,
        protected bool   $visible = true,
        protected array  $formatters = [],
    ) {
        $formatters[] = new ToOneFormatter();
        parent::__construct(
            key: $key,
            name: $name,
            visible: $visible,
            formatters: $formatters,
        );
    }

    public function renderer(): string
    {
        return 'join-one-column-renderer';
    }
}
