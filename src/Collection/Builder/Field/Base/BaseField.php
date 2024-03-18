<?php
declare(strict_types=1);

namespace Megio\Collection\Builder\Field\Base;

use Megio\Collection\Builder\Rule\Base\IRule;

abstract class BaseField implements IField
{
    protected string|int|float|bool|null $value = null;
    
    /** @var string[] */
    protected array $errors = [];
    
    /**
     * @param string $name
     * @param string $label
     * @param \Megio\Collection\Builder\Rule\Base\IRule[] $rules
     * @param array<string, string|int|float|bool|null> $attrs
     * @param bool $mapToEntity
     * @param \Megio\Collection\Builder\Field\Base\FieldNativeType $type
     */
    public function __construct(
        protected string          $name,
        protected string          $label,
        protected array           $rules = [],
        protected array           $attrs = [],
        protected bool            $mapToEntity = true,
        protected FieldNativeType $type = FieldNativeType::CUSTOM
    )
    {
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getLabel(): string
    {
        return $this->label;
    }
    
    public function addRule(IRule $rule): void
    {
        $this->rules[] = $rule;
    }
    
    public function getRules(): array
    {
        return $this->rules;
    }
    
    /** @return array<string, string|int|float|bool|null> */
    public function getAttrs(): array
    {
        return $this->attrs;
    }
    
    public function getType(): FieldNativeType
    {
        return $this->type;
    }
    
    public function mappedToEntity(): bool
    {
        return $this->mapToEntity;
    }
    
    public function getValue(): string|int|float|bool|null
    {
        return $this->value;
    }
    
    public function setValue(string|int|float|bool|null $value): void
    {
        $this->value = $value;
    }
    
    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }
    
    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'type' => $this->getType()->value,
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'rules' => array_map(fn($rule) => $rule->toArray(), $this->getRules()),
            'attrs' => $this->getAttrs(),
            'value' => $this->getValue(),
            'errors' => $this->getErrors(),
        ];
    }
}