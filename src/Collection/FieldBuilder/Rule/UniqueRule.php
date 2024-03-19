<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Rule;

use Doctrine\ORM\AbstractQuery;
use Megio\Collection\FieldBuilder\FieldBuilderEvent;
use Megio\Collection\FieldBuilder\Rule\Base\BaseRule;

class UniqueRule extends BaseRule
{
    /**
     * @param string $columnName
     * @param class-string $entityClassName
     * @param string|null $message
     */
    public function __construct(
        protected string      $entityClassName,
        protected string      $columnName,
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
     */
    public function validate(): bool
    {
        $repo = $this->getEntityManager()->getRepository($this->entityClassName);
        
        $row = $repo->createQueryBuilder('e')
            ->select('e')
            ->where("e.{$this->columnName} = :value")
            ->setParameter('value', $this->getValue())
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY)
        ;
        
        if ($row === null) {
            return true;
        }
        
        $updating = $this->getBuilder()->getEvent() === FieldBuilderEvent::UPDATE;
        
        if ($row && $updating && $row[$this->columnName] === $this->getValue()) {
            return true;
        }
        
        return false;
    }
}