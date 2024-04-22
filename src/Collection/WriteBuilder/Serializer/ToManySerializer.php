<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use Megio\Collection\Exception\SerializerException;
use Megio\Collection\WriteBuilder\Field\Base\IField;
use Megio\Collection\WriteBuilder\Serializer\Base\BaseSerializer;

class ToManySerializer extends BaseSerializer
{
    /**
     * @param class-string $targetEntity
     */
    public function __construct(
        protected string $targetEntity,
        protected string $columnKey = 'id',
    )
    {
    }
    
    public function serialize(IField $field): mixed
    {
        $value = $field->getValue();
        
        if ($value === null) {
            return null;
        }
        
        if (!is_array($value)) {
            throw new SerializerException("Invalid OneToMany serializer value in field '{$field->getName()}'");
        }
        
        $em = $this->getBuilder()->getEntityManager();
        
        $rows = $em
            ->getRepository($this->targetEntity)
            ->findBy([$this->columnKey => $value]);
        
        return new ArrayCollection($rows);
    }
}