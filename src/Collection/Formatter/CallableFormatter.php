<?php
declare(strict_types=1);

namespace Megio\Collection\Formatter;

use Megio\Collection\ReadBuilder\Column\Base\ShowOnlyOn;
use Megio\Collection\Formatter\Base\BaseFormatter;

class CallableFormatter extends BaseFormatter
{
    /**
     * @var array<int, callable> $callback
     */
    protected array $callback;
    
    /**
     * @param callable $callback
     * @param \Megio\Collection\ReadBuilder\Column\Base\ShowOnlyOn|null $showOnlyOn
     */
    public function __construct(callable $callback, protected ?ShowOnlyOn $showOnlyOn = null)
    {
        $this->callback = [$callback];
        parent::__construct($showOnlyOn);
    }
    
    public function format(mixed $value): mixed
    {
        return $this->callback[0]($value, $this);
    }
}