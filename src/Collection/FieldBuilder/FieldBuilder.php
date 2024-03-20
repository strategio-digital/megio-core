<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder;

use Doctrine\ORM\EntityManagerInterface;
use Megio\Collection\FieldBuilder\Field\Base\IField;
use Megio\Collection\FieldBuilder\Field\Base\UndefinedValue;
use Megio\Collection\FieldBuilder\Rule\BooleanRule;
use Megio\Collection\FieldBuilder\Rule\DateTimeCzRule;
use Megio\Collection\FieldBuilder\Rule\DecimalRule;
use Megio\Collection\FieldBuilder\Rule\IntegerRule;
use Megio\Collection\FieldBuilder\Rule\MaxRule;
use Megio\Collection\FieldBuilder\Rule\NullableRule;
use Megio\Collection\FieldBuilder\Rule\StringRule;
use Megio\Collection\FieldBuilder\Rule\UniqueRule;
use Megio\Collection\CollectionPropType;
use Megio\Collection\ICollectionRecipe;
use Megio\Collection\RecipeEntityMetadata;

class FieldBuilder
{
    /** @var IField[] */
    protected array $fields = [];
    
    /** @var array<string, mixed> */
    protected array $buildFields = [];
    
    /** @var array<string, string[]> */
    protected array $errors = [];
    
    protected RecipeEntityMetadata $metadata;
    
    /** @var array{name: string, type: string, unique: bool, nullable: bool, maxLength: int|null}[] */
    protected array $dbSchema = [];
    
    
    protected ICollectionRecipe $recipe;
    
    protected string|null $rowId = null;
    
    /** @var array<string, string|int|float|bool|null> */
    protected array $values = [];
    
    /** @var array<string, string[]> */
    protected array $ignoredRules = [];
    
    protected bool $ignoreDoctrineRules = false;
    
    protected FieldBuilderEvent $event;
    
    public function __construct(
        protected readonly EntityManagerInterface $em
    )
    {
    }
    
    /**
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @param \Megio\Collection\FieldBuilder\FieldBuilderEvent $event
     * @param array<string, string|int|float|bool|null> $values
     * @param string|null $rowId
     * @return $this
     */
    public function create(ICollectionRecipe $recipe, FieldBuilderEvent $event, array $values = [], string|null $rowId = null): self
    {
        $this->recipe = $recipe;
        $this->values = $values;
        $this->event = $event;
        $this->rowId = $rowId;
        return $this;
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
        $this->metadata = $this->recipe->getEntityMetadata(CollectionPropType::NONE);
        $this->dbSchema = $this->metadata->getFullSchemaReflectedByDoctrine();
        
        foreach ($this->fields as $field) {
            $field->setBuilder($this);
            
            $columnSchema = current(array_filter($this->dbSchema, fn($f) => $f['name'] === $field->getName()));
            
            if (!$this->ignoreDoctrineRules && $columnSchema) {
                $field = $this->createRulesByDbSchema($field, $columnSchema);
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
    
    public function validate(): self
    {
        $fieldNames = array_keys($this->buildFields);
        $valueNames = array_keys($this->values);
        
        foreach ($valueNames as $valueName) {
            if (!in_array($valueName, $fieldNames)) {
                $this->errors['@'][] = "Field '{$valueName}' is not defined in '{$this->recipe->name()}' recipe for '{$this->event->name}' action";
            }
        }
        
        foreach ($this->fields as $field) {
            $ignoredFieldRules = array_key_exists($field->getName(), $this->ignoredRules)
                ? $this->ignoredRules[$field->getName()]
                : [];
            
            $nullable = count(array_filter($field->getRules(), fn($rule) => $rule->name() === 'nullable')) !== 0;
            $required = count(array_filter($field->getRules(), fn($rule) => $rule->name() === 'required')) !== 0;
            $ignore = $field->getValue() instanceof UndefinedValue === true && $nullable === false && $required === false;
            
            foreach ($field->getRules() as $rule) {
                if (!$ignore && !in_array($rule->name(), $ignoredFieldRules) && $rule->validate() === false) {
                    $field->addError($rule->message());
                    $this->errors[$field->getName()][] = $rule->message();
                }
            }
        }
        
        return $this;
    }
    
    /**
     * @param array<string, string[]> $rules
     */
    public function ignoreRules(array $rules): self
    {
        $this->ignoredRules = $rules;
        return $this;
    }
    
    public function ignoreDoctrineRules(): self
    {
        $this->ignoreDoctrineRules = true;
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
    
    public function getRecipe(): ICollectionRecipe
    {
        return $this->recipe;
    }
    
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }
    
    public function getEvent(): FieldBuilderEvent
    {
        return $this->event;
    }
    
    public function getMetadata(): RecipeEntityMetadata
    {
        return $this->metadata;
    }
    
    public function getRowId(): string|null
    {
        return $this->rowId;
    }
    
    /**
     * @return array{name: string, type: string, unique: bool, nullable: bool, maxLength: int|null}[]
     */
    public function getDbSchema(): array
    {
        return $this->dbSchema;
    }
    
    /**
     * @return array<int, mixed>
     */
    public function toArray(): array
    {
        return array_values($this->buildFields);
    }
    
    /**
     * @return array<string, string|int|float|bool|null|array<string,mixed>>
     */
    public function toClearValues(): array
    {
        $values = [];
        
        foreach ($this->fields as $field) {
            if ($field->mappedToEntity() === true) {
                if ($field->getValue() instanceof UndefinedValue === false) {
                    $values[$field->getName()] = $field->getValue();
                }
            }
        }
        
        return $values;
    }
    
    /**
     * @param \Megio\Collection\FieldBuilder\Field\Base\IField $field
     * @param array{name: string, type: string, unique: bool, nullable: bool, maxLength: int|null} $columnSchema
     * @return IField
     */
    protected function createRulesByDbSchema(IField $field, array $columnSchema): IField
    {
        $ruleNames = array_map(fn($rule) => $rule->name(), $field->getRules());
        
        /** @var \Megio\Collection\FieldBuilder\Rule\Base\IRule[] $typeMap */
        $typeMap = [
            'string' => new StringRule(),
            'integer' => new IntegerRule(),
            'float' => new DecimalRule(),
            'boolean' => new BooleanRule(),
            'datetime' => new DateTimeCzRule(),
        ];
        
        foreach ($typeMap as $type => $value) {
            if (!in_array($value->name(), $ruleNames) && $columnSchema['type'] === $type) {
                $field->addRule($value);
            }
        }
        
        if (!in_array('nullable', $ruleNames) && $columnSchema['nullable'] === true) {
            $field->addRule(new NullableRule());
        }
        
        if (!in_array('max', $ruleNames) && $columnSchema['maxLength'] !== null) {
            $field->addRule(new MaxRule($columnSchema['maxLength']));
        }
        
        if (!in_array('unique', $ruleNames) && $columnSchema['unique'] === true) {
            $field->addRule(new UniqueRule($this->recipe->source(), $field->getName()));
        }
        
        return $field;
    }
}