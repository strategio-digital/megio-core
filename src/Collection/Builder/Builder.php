<?php
declare(strict_types=1);

namespace Megio\Collection\Builder;

use Megio\Collection\Builder\Field\Base\IField;
use Megio\Collection\Builder\Rule\MaxRule;
use Megio\Collection\Builder\Rule\NullableRule;
use Megio\Collection\CollectionPropType;
use Megio\Collection\ICollectionRecipe;

class Builder
{
    /** @var IField[] */
    private array $fields = [];
    
    /** @var array<string, mixed> */
    private array $buildFields = [];
    
    /** @var array<string, string[]> */
    private array $errors = [];
    
    /**
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @param array<string, string|int|float|bool|null> $values
     */
    public function __construct(
        protected readonly ICollectionRecipe $recipe,
        protected readonly array             $values = [],
    )
    {
    }
    
    public function add(IField $field): self
    {
        $this->override($field);
        return $this;
    }
    
    public function override(IField $field): self
    {
        $this->fields[$field->getName()] = $field;
        return $this;
    }
    
    public function build(): self
    {
        $metadata = $this->recipe->getEntityMetadata(CollectionPropType::NONE);
        $dbSchema = $metadata->getFullSchemaReflectedByDoctrine();
        
        foreach ($this->fields as $field) {
            if ($schema = current(array_filter($dbSchema, fn($f) => $f['name'] === $field->getName()))) {
                $field = $this->createRulesByDbSchema($field, $schema);
            }
            
            $rules = $field->getRules();
            
            foreach ($rules as $rule) {
                $rule->setField($field);
                $rule->setRelatedFields($this->fields);
                $rule->setRelatedRules($rules);
            }
            
            if (array_key_exists($field->getName(), $this->values)) {
                $field->setValue($this->values[$field->getName()]);
            }
            
            $this->buildFields[$field->getName()] = $field->toArray();
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function validate(): self
    {
        $fieldNames = array_keys($this->buildFields);
        $valueNames = array_keys($this->values);
        
        foreach ($valueNames as $valueName) {
            if (!in_array($valueName, $fieldNames)) {
                $this->errors['@'][] = "Field '{$valueName}' is not defined in '{$this->recipe->name()}' recipe for this action";
            }
        }
        
        foreach ($this->fields as $field) {
            foreach ($field->getRules() as $rule) {
                if ($rule->validate() === false) {
                    $field->addError($rule->message());
                    $this->errors[$field->getName()][] = $rule->message();
                }
            }
        }
        
        return $this;
    }
    
    public function countFields(): int
    {
        return count($this->buildFields);
    }
    
    public function isValid(): bool
    {
        return array_reduce($this->errors, fn($sum, $items) => $sum + count($items), 0) === 0;
    }
    
    /**
     * @return array<string, string[]>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * @return array<int, mixed>
     */
    public function toArray(): array
    {
        return array_values($this->buildFields);
    }
    
    /**
     * @return array<string, string|int|float|bool|null>
     */
    public function toClearValues(): array
    {
        $values = [];
        
        foreach ($this->fields as $field) {
            $required = count(array_filter($field->getRules(), fn($rule) => $rule->name() === 'required')) !== 0;
            $ignored = $field->getValue() === null && $required === false;
            
            if ($field->mappedToEntity() === true && $ignored === false) {
                $values[$field->getName()] = $field->getValue();
            }
        }
        
        return $values;
    }
    
    /**
     * @param \Megio\Collection\Builder\Field\Base\IField $field
     * @param array{maxLength: int|null, name: string, nullable: bool, type: string} $schema
     * @return IField
     */
    protected function createRulesByDbSchema(IField $field, array $schema): IField
    {
        $ruleNames = array_map(fn($rule) => $rule->name(), $field->getRules());
        
        if (!in_array('nullable', $ruleNames) && $schema['nullable'] === true) {
            $field->addRule(new NullableRule());
        }
        
        if (!in_array('max', $ruleNames) && $schema['maxLength'] !== null) {
            $field->addRule(new MaxRule($schema['maxLength']));
        }
        
        return $field;
    }
}