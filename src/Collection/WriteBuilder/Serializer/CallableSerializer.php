<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Serializer;

use Megio\Collection\WriteBuilder\Serializer\Base\BaseSerializer;

class CallableSerializer extends BaseSerializer
{
    /**
     * @var array<int, callable> $callback
     */
    protected array $callback;
    
    public function __construct(callable $callback)
    {
        $this->callback = [$callback];
    }
    
    public function serialize(mixed $value): mixed
    {
        return $this->callback[0]($value);
    }
}