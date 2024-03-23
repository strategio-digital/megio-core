<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder\Transformer;

use Megio\Collection\ReadBuilder\Transformer\Base\BaseTransformer;

class CallableTransformer extends BaseTransformer
{
    /**
     * @var array<int, callable> $callback
     */
    protected array $callback;
    
    /**
     * @param callable $callback
     * @param bool $adminPanelOnly
     */
    public function __construct(callable $callback, bool $adminPanelOnly = false)
    {
        $this->callback = [$callback];
        parent::__construct($adminPanelOnly);
    }
    
    public function transform(mixed $value): mixed
    {
        return $this->callback[0]($value, $this);
    }
}