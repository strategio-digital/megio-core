<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Megio\Collection\IRecipeBuilder;
use Megio\Collection\WriteBuilder\Field\Base\IField;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Rule\ArrayRule;
use Megio\Collection\WriteBuilder\Rule\Base\IRule;
use Megio\Collection\WriteBuilder\Rule\BooleanRule;
use Megio\Collection\WriteBuilder\Rule\DateRule;
use Megio\Collection\WriteBuilder\Rule\DateTimeIntervalRule;
use Megio\Collection\WriteBuilder\Rule\DateTimeRule;
use Megio\Collection\WriteBuilder\Rule\DecimalRule;
use Megio\Collection\WriteBuilder\Rule\IntegerRule;
use Megio\Collection\WriteBuilder\Rule\JsonRule;
use Megio\Collection\WriteBuilder\Rule\MaxRule;
use Megio\Collection\WriteBuilder\Rule\NullableRule;
use Megio\Collection\WriteBuilder\Rule\RequiredRule;
use Megio\Collection\WriteBuilder\Rule\StringRule;
use Megio\Collection\WriteBuilder\Rule\TimeRule;
use Megio\Collection\WriteBuilder\Rule\UniqueRule;
use Megio\Collection\ICollectionRecipe;
use Megio\Collection\RecipeEntityMetadata;

class WriteBuilder implements IRecipeBuilder
{
    protected WriteBuilderEvent $event;
    
    protected ICollectionRecipe $recipe;
    
    protected RecipeEntityMetadata $metadata;
    
    protected string|null $rowId = null;
    
    /** @var IField[] */
    protected array $fields = [];
    
    /** @var array<string, mixed> */
    protected array $buildFields = [];
    
    /** @var array<string, string[]> */
    protected array $errors = [];
    
    /** @var array{name: string, type: string, unique: bool, nullable: bool, maxLength: int|null}[] */
    protected array $dbSchema = [];
    
    /** @var array<string, string|int|float|bool|null> */
    protected array $values = [];
    
    /** @var array<string, class-string[]> */
    protected array $ignoredRules = [];
    
    protected bool $ignoreSchemaRules = false;
    
    public function __construct(
        protected readonly EntityManagerInterface $em
    )
    {
    }
    
    /**
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @param \Megio\Collection\WriteBuilder\WriteBuilderEvent $event
     * @param array<string, string|int|float|bool|null> $values
     * @param string|null $rowId
     * @return $this
     */
    public function create(ICollectionRecipe $recipe, WriteBuilderEvent $event, array $values = [], string|null $rowId = null): self
    {
        $this->recipe = $recipe;
        $this->values = $values;
        $this->event = $event;
        $this->rowId = $rowId;
        return $this;
    }
    
    public function add(IField $field): self
    {
        $this->fields[$field->getName()] = $field;
        return $this;
    }
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function build(): self
    {
        $this->metadata = $this->recipe->getEntityMetadata();
        $this->dbSchema = $this->metadata->getFullSchemaReflectedByDoctrine();
        
        foreach ($this->fields as $field) {
            $field->setBuilder($this);
            
            $columnSchema = current(array_filter($this->dbSchema, fn($f) => $f['name'] === $field->getName()));
            
            if (!$this->ignoreSchemaRules && $columnSchema) {
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
            
            if ($field->isDisabled() === false) {
                $nullable = count(array_filter($field->getRules(), fn($rule) => $rule::class === NullableRule::class)) !== 0;
                $required = count(array_filter($field->getRules(), fn($rule) => $rule::class === RequiredRule::class)) !== 0;
                $ignore = $field->getValue() instanceof UndefinedValue === true && $nullable === false && $required === false;
                
                foreach ($field->getRules() as $rule) {
                    if (!$ignore && !in_array($rule::class, $ignoredFieldRules) && $rule->validate() === false) {
                        $field->addError($rule->message());
                        $this->errors[$field->getName()][] = $rule->message();
                    }
                }
            }
        }
        
        return $this;
    }
    
    /**
     * @param array<string, class-string[]> $rules
     */
    public function ignoreRules(array $rules): self
    {
        $this->ignoredRules = $rules;
        return $this;
    }
    
    public function ignoreSchemaRules(): self
    {
        $this->ignoreSchemaRules = true;
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
    
    public function getEvent(): WriteBuilderEvent
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
            if ($field->mappedToEntity() === true && $field->isDisabled() === false) {
                if ($field->getValue() instanceof UndefinedValue === false) {
                    $values[$field->getName()] = $field->getValue();
                }
            }
        }
        
        return $values;
    }
    
    /**
     * @param \Megio\Collection\WriteBuilder\Field\Base\IField $field
     * @param array{name: string, type: string, unique: bool, nullable: bool, maxLength: int|null} $columnSchema
     * @return IField
     */
    protected function createRulesByDbSchema(IField $field, array $columnSchema): IField
    {
        $ruleClassNames = array_map(fn($rule) => $rule::class, $field->getRules());
        
        $rule = $this->createRuleInstance($columnSchema['type']);
        if ($rule !== null && !in_array($rule::class, $ruleClassNames)) {
            $field->addRule($rule);
        }
        
        if (!in_array(NullableRule::class, $ruleClassNames) && $columnSchema['nullable'] === true) {
            $field->addRule(new NullableRule());
        }
        
        if (!in_array(MaxRule::class, $ruleClassNames) && $columnSchema['maxLength'] !== null) {
            $field->addRule(new MaxRule($columnSchema['maxLength']));
        }
        
        if (!in_array(UniqueRule::class, $ruleClassNames) && $columnSchema['unique'] === true) {
            $field->addRule(new UniqueRule($this->recipe->source(), $field->getName()));
        }
        
        return $field;
    }
    
    protected function createRuleInstance(string $type): ?IRule
    {
        return match ($type) {
            Types::ASCII_STRING,
            Types::BIGINT,
            Types::BINARY,
            Types::GUID,
            Types::STRING,
            Types::BLOB,
            Types::TEXT => new StringRule(),
            
            Types::DECIMAL,
            Types::FLOAT => new DecimalRule(),
            
            Types::BOOLEAN => new BooleanRule(),
            
            Types::DATE_MUTABLE,
            Types::DATE_IMMUTABLE => new DateRule(),
            Types::DATEINTERVAL => new DateTimeIntervalRule(),
            
            Types::DATETIME_MUTABLE,
            Types::DATETIME_IMMUTABLE,
            Types::DATETIMETZ_MUTABLE,
            Types::DATETIMETZ_IMMUTABLE => new DateTimeRule(),
            
            Types::INTEGER,
            Types::SMALLINT => new IntegerRule(),
            
            Types::JSON => new JsonRule(),
            Types::SIMPLE_ARRAY => new ArrayRule(),
            
            Types::TIME_MUTABLE,
            Types::TIME_IMMUTABLE => new TimeRule(),
            
            default => null,
        };
    }
}