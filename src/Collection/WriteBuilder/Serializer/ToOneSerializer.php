<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Serializer;

use Megio\Collection\Exception\SerializerException;
use Megio\Collection\WriteBuilder\Field\Base\IField;
use Megio\Collection\WriteBuilder\Serializer\Base\BaseSerializer;

class ToOneSerializer extends BaseSerializer
{
    /**
     * @param class-string $entityClassName
     */
    public function __construct(
        protected string $entityClassName,
        protected string $primaryKey = 'id',
    )
    {
    }
    
    public function serialize(IField $field): mixed
    {
        $value = $field->getValue();
        
        if ($value === null) {
            return null;
        }
        
        if (!is_string($value) && !is_numeric($value) && !is_bool($value)) {
            throw new SerializerException("Invalid OneToOne serializer value in field '{$field->getName()}'");
        }
        
        $em = $this->getBuilder()->getEntityManager();
        
        $row = $em
            ->getRepository($this->entityClassName)
            ->findOneBy([$this->primaryKey => $value]);
        
        if ($row) {
            return $row;
        }
        
        throw new SerializerException("Invalid OneToOne serializer value in field '{$field->getName()}'");
    }
}