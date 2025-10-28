<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use Megio\Collection\Formatter\Base\BaseFormatter;
use Megio\Collection\ReadBuilder\Column\Base\ShowOnlyOn;

class CallableFormatter extends BaseFormatter
{
    /**
     * @var array<int, callable>
     */
    protected array $callback;

    /**
     */
    public function __construct(
        callable $callback,
        protected ?ShowOnlyOn $showOnlyOn = null,
    ) {
        $this->callback = [$callback];
        parent::__construct($showOnlyOn);
    }

    public function format(
        mixed $value,
        string $key,
    ): mixed {
        return $this->callback[0]($value, $this);
    }
}
