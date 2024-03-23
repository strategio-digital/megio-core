<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule\Base;

use Doctrine\ORM\EntityManagerInterface;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\WriteBuilder\Field\Base\IField;

abstract class BaseRule implements IRule
{
    protected IField $field;
    
    /** @var IField[] */
    protected array $relatedFields = [];
    
    /** @var IRule[] */
    protected array $relatedRules = [];
    
    public function __construct(protected string|null $message = null)
    {
    }
    
    public function setField(IField $field): void
    {
        $this->field = $field;
    }
    
    public function getField(): IField
    {
        return $this->field;
    }
    
    public function setRelatedFields(array $fields): void
    {
        $this->relatedFields = $fields;
    }
    
    public function setRelatedRules(array $rules): void
    {
        $this->relatedRules = $rules;
    }
    
    /**
     * Shortcut to get the entity manager
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->getBuilder()->getEntityManager();
    }
    
    /**
     * Shortcut to get the current builder
     */
    public function getBuilder(): WriteBuilder
    {
        return $this->field->getBuilder();
    }
    
    /**
     * Shortcut to get the current field value
     * @return string|int|float|bool|null|array<string,mixed>|UndefinedValue
     */
    public function getValue(): string|int|float|bool|null|array|UndefinedValue
    {
        return $this->field->getValue();
    }
    
    public function name(): string
    {
        $namespace = explode('\\', $this::class);
        
        /** @var string $className */
        $className = end($namespace);
        
        return $className;
    }
    
    /**
     * Structured description for usage in front-end form
     * @return array{name: string, message: string, params: array<string,mixed>}
     */
    public function toArray(): array
    {
        return [
            'name' =>  $this->name(),
            'message' => $this->message(),
            'params' => [],
        ];
    }
}