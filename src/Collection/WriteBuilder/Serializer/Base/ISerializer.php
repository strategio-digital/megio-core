<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Serializer\Base;

use Megio\Collection\Exception\SerializerException;

interface ISerializer
{
    /**
     * @throws SerializerException
     */
    public function serialize(mixed $value): mixed;
}