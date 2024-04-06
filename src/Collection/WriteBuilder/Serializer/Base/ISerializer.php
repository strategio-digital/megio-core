<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Serializer\Base;

use Megio\Collection\Exception\SerializerException;
use Megio\Collection\WriteBuilder\Field\Base\IField;
use Megio\Collection\WriteBuilder\WriteBuilder;

interface ISerializer
{
    /** @throws SerializerException */
    public function serialize(IField $field): mixed;
    
    public function setBuilder(WriteBuilder $builder): void;
    
    public function getBuilder(): WriteBuilder;
}