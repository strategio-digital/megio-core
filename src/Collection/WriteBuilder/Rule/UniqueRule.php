<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Doctrine\ORM\AbstractQuery;
use Megio\Collection\Exception\InvalidArgumentException;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class UniqueRule extends BaseRule
{
    /**
     * @param class-string $targetEntity
     */
    public function __construct(
        protected string  $targetEntity,
        protected string  $columnName,
        protected string  $primaryKey = 'id',
        protected ?string $message = null
    )
    {
        parent::__construct($message);
    }
    
    public function message(): string
    {
        return $this->message ?: "Value must be unique";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Megio\Collection\Exception\InvalidArgumentException
     */
    public function validate(): bool
    {
        $value = $this->getValue();
        
        if (!is_string($value) && !is_numeric($value) && !is_bool($value)) {
            return false;
        }
        
        $repo = $this->getEntityManager()->getRepository($this->targetEntity);
        
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
            throw new InvalidArgumentException("Property '{$this->primaryKey}' not found in entity '{$this->targetEntity}'");
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