<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Doctrine\ORM\AbstractQuery;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class UniqueRule extends BaseRule
{
    /**
     * @param class-string $entityClassName
     * @param string $columnName
     * @param string $primaryKey
     * @param string|null $message
     */
    public function __construct(
        protected string      $entityClassName,
        protected string      $columnName,
        protected string      $primaryKey = 'id',
        protected string|null $message = null
    )
    {
        parent::__construct($message);
    }
    
    public function name(): string
    {
        return 'unique';
    }
    
    public function message(): string
    {
        return $this->message ?: "Value of '{$this->getField()->getName()}' must be unique";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function validate(): bool
    {
        $repo = $this->getEntityManager()->getRepository($this->entityClassName);
        
        $row = $repo->createQueryBuilder('e')
            ->select('e')
            ->where("e.{$this->columnName} = :value")
            ->setParameter('value', $this->getValue())
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        
        if ($row === null) {
            return true;
        }
        
        if (!array_key_exists($this->primaryKey, $row)) {
            throw new CollectionException("Property '{$this->primaryKey}' not found in entity '{$this->entityClassName}'");
        }
        
        if (
            $row
            && $row[$this->columnName] === $this->getValue()
            && $row[$this->primaryKey] === $this->getBuilder()->getRowId()
        ) {
            return true;
        }
        
        return false;
    }
}