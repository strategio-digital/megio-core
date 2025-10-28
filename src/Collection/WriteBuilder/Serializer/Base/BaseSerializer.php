<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Serializer\Base;

use Megio\Collection\WriteBuilder\WriteBuilder;

abstract class BaseSerializer implements ISerializer
{
    protected WriteBuilder $builder;

    public function setBuilder(WriteBuilder $builder): void
    {
        $this->builder = $builder;
    }

    public function getBuilder(): WriteBuilder
    {
        return $this->builder;
    }
}
