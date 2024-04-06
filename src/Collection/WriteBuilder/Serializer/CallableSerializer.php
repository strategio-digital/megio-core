<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Serializer;

use Megio\Collection\WriteBuilder\Field\Base\IField;
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
    
    public function serialize(IField $field): mixed
    {
        return $this->callback[0]($field->getValue());
    }
}