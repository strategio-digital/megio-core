<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Megio\Collection\IRecipeBuilder;
use Megio\Collection\WriteBuilder\Field\ArrayField;
use Megio\Collection\WriteBuilder\Field\Base\IField;
use Megio\Collection\WriteBuilder\Field\Base\PureField;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Field\DateField;
use Megio\Collection\WriteBuilder\Field\DateTimeField;
use Megio\Collection\WriteBuilder\Field\DateTimeIntervalField;
use Megio\Collection\WriteBuilder\Field\DecimalField;
use Megio\Collection\WriteBuilder\Field\EmailField;
use Megio\Collection\WriteBuilder\Field\IntegerField;
use Megio\Collection\WriteBuilder\Field\JsonField;
use Megio\Collection\WriteBuilder\Field\PasswordField;
use Megio\Collection\WriteBuilder\Field\PhoneCzField;
use Megio\Collection\WriteBuilder\Field\SlugField;
use Megio\Collection\WriteBuilder\Field\TextField;
use Megio\Collection\WriteBuilder\Field\TimeField;
use Megio\Collection\WriteBuilder\Field\ToggleBtnField;
use Megio\Collection\WriteBuilder\Field\UrlField;
use Megio\Collection\WriteBuilder\Field\VideoLinkField;
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
use Megio\Helper\ArrayMove;
use Nette\Utils\Strings;

class WriteBuilder implements IRecipeBuilder
{
    private WriteBuilderEvent $event;
    
    private ICollectionRecipe $recipe;
    
    private RecipeEntityMetadata $metadata;
    
    private ?string $rowId = null;
    
    /** @var array<string, IField> */
    private array $fields = [];
    
    /** @var array<string, string[]> */
    private array $errors = [];
    
    /** @var array{name: string, type: string, unique: bool, nullable: bool, maxLength: int|null}[] */
    private array $dbSchema = [];
    
    /** @var array<string, string|int|float|bool|null> */
    private array $values = [];
    
    /** @var array<string, class-string[]> */
    private array $ignoredRules = [];
    
    private bool $keepDbSchema = true;
    
    public function __construct(protected readonly EntityManagerInterface $em)
    {
    }
    
    /**
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @param \Megio\Collection\WriteBuilder\WriteBuilderEvent $event
     * @param array<string, string|int|float|bool|null> $values
     * @param string|null $rowId
     * @return $this
     */
    public function create(ICollectionRecipe $recipe, WriteBuilderEvent $event, array $values = [], string $rowId = null): self
    {
        $this->recipe = $recipe;
        $this->values = $values;
        $this->event = $event;
        $this->rowId = $rowId;
        return $this;
    }
    
    public function add(IField $field, string $moveBeforeName = null, string $moveAfterName = null): self
    {
        if ($this->keepDbSchema === false) {
            $this->fields = [];
            $this->keepDbSchema = true;
        }
        
        $field->setBuilder($this);
        $this->fields[$field->getName()] = $field;
        
        if ($moveBeforeName !== null) {
            $this->fields = ArrayMove::moveBefore($this->fields, $field->getName(), $moveBeforeName);
        }
        
        if ($moveAfterName !== null) {
            $this->fields = ArrayMove::moveAfter($this->fields, $field->getName(), $moveAfterName);
        }
        
        return $this;
    }
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function build(): self
    {
        $this->metadata = $this->recipe->getEntityMetadata();
        $this->dbSchema = $this->metadata->getFullSchemaReflectedByDoctrine();
        
        $this->createValues();
        
        return $this;
    }
    
    /**
     * @param string[] $exclude
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function buildByDbSchema(array $exclude = [], bool $persist = false): self
    {
        $this->metadata = $this->recipe->getEntityMetadata();
        $this->dbSchema = $this->metadata->getFullSchemaReflectedByDoctrine();
        
        $ignored = array_merge($exclude, ['id']);
        
        foreach ($this->dbSchema as $columnSchema) {
            if (!in_array($columnSchema['name'], $ignored)) {
                $field = $this->createFieldInstanceByColumnType($columnSchema['type'], $columnSchema['name']);
                $field->setBuilder($this);
                
                $field = $this->createRulesByDbSchema($field, $columnSchema);
                $this->fields[$field->getName()] = $field;
            }
        }
        
        $this->fields = ArrayMove::moveToStart($this->fields, 'id');
        $this->fields = ArrayMove::moveToEnd($this->fields, 'createdAt');
        $this->fields = ArrayMove::moveToEnd($this->fields, 'updatedAt');
        
        $this->createValues();
        
        $this->keepDbSchema = $persist;
        
        return $this;
    }
    
    public function validate(): self
    {
        $fieldNames = array_keys($this->fields);
        $valueNames = array_keys($this->values);
        
        foreach ($valueNames as $valueName) {
            if (!in_array($valueName, $fieldNames)) {
                $this->errors['@'][] = "Field '{$valueName}' is not defined in '{$this->recipe->key()}' recipe for '{$this->event->name}' action";
            }
        }
        
        foreach ($this->fields as $field) {
            if ($field->isDisabled() === false) {
                $valueIsUndefined = $field->getValue() instanceof UndefinedValue;
                $required = count(array_filter($field->getRules(), fn($rule) => $rule::class === RequiredRule::class)) !== 0;
                $nullable = count(array_filter($field->getRules(), fn($rule) => $rule::class === NullableRule::class)) !== 0;
                
                $validate = false;
                
                if (!$valueIsUndefined) {
                    $validate = true;
                }
                
                if ($valueIsUndefined && $required) {
                    $validate = true;
                }
                
                if ($nullable && $field->getValue() === null) {
                    $validate = false;
                }
                
                foreach ($field->getRules() as $rule) {
                    if ($validate && $rule->validate() === false) {
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
    
    public function countFields(): int
    {
        return count($this->fields);
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
    
    public function getRowId(): ?string
    {
        return $this->rowId;
    }
    
    /**
     * @return array<int, mixed>
     */
    public function toArray(): array
    {
        foreach ($this->fields as $field) {
            $formatters = $field->getFormatters();
            foreach ($formatters as $formatter) {
                $field->setValue($formatter->format($field->getValue()));
            }
        }
        
        $fields = array_values(array_map(fn($field) => $field->toArray(), $this->fields));
        
        foreach ($fields as $key => $field) {
            if ($field['default_value'] instanceof UndefinedValue) {
                unset($fields[$key]['default_value']);
            }
            
            if ($field['value'] instanceof UndefinedValue) {
                unset($fields[$key]['value']);
            }
        }
        
        return $fields;
    }
    
    public function dump(): void
    {
        dumpe($this->build()->toArray());
    }
    
    /**
     * @return array<string, mixed>
     * @throws \Megio\Collection\Exception\SerializerException
     */
    public function getSerializedValues(): array
    {
        $values = [];
        
        foreach ($this->fields as $field) {
            if ($field->mappedToEntity() === true && $field->isDisabled() === false) {
                if ($field->getValue() instanceof UndefinedValue === false) {
                    foreach ($field->getSerializers() as $serializer) {
                        $field->setValue($serializer->serialize($field->getValue()));
                    }
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
        
        $rule = $this->createRuleInstanceByColumnType($columnSchema['type']);
        
        if ($rule !== null && !in_array($rule::class, $ruleClassNames)) {
            $field->addRule($rule);
        }
        
        if ($columnSchema['nullable'] === true && !in_array(NullableRule::class, $ruleClassNames)) {
            $field->addRule(new NullableRule());
        }
        
        if ($columnSchema['maxLength'] !== null && !in_array(MaxRule::class, $ruleClassNames)) {
            $field->addRule(new MaxRule($columnSchema['maxLength']));
        }
        
        if ($columnSchema['unique'] === true && !in_array(UniqueRule::class, $ruleClassNames)) {
            $field->addRule(new UniqueRule($this->recipe->source(), $field->getName()));
        }
        
        return $field;
    }
    
    public function recreateRules(IField $field): IField
    {
        $rules = $field->getRules();
        
        $ignoredRules = array_key_exists($field->getName(), $this->ignoredRules)
            ? $this->ignoredRules[$field->getName()]
            : [];
        
        foreach ($rules as $rule) {
            if (in_array($rule::class, $ignoredRules)) {
                $field->removeRule($rule);
            }
            
            $rule->setField($field);
            $rule->setRelatedFields($this->fields);
            $rule->setRelatedRules($rules);
        }
        
        return $field;
    }
    
    /**
     * @return void
     */
    public function createValues(): void
    {
        foreach ($this->fields as $key => $field) {
            $this->fields[$key] = $this->recreateRules($field);
            if (array_key_exists($field->getName(), $this->values)) {
                $field->setValue($this->values[$field->getName()]);
            }
        }
    }
    
    protected function createRuleInstanceByColumnType(string $type): ?IRule
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
    
    protected function createFieldInstanceByColumnType(string $type, string $name): IField
    {
        $namesMap = [
            'password' => new PasswordField($name, $name),
            'email' => new EmailField($name, $name),
            'phone_cz' => new PhoneCzField($name, $name),
            'slug' => new SlugField($name, $name),
            'url' => new UrlField($name, $name),
            'video' => new VideoLinkField($name, $name),
        ];
        
        $fieldByName = null;
        foreach ($namesMap as $key => $field) {
            if (Strings::contains($name, $key)) {
                $fieldByName = $field;
            }
        }
        
        return match ($type) {
            Types::ASCII_STRING,
            Types::BIGINT,
            Types::BINARY,
            Types::GUID,
            Types::STRING,
            Types::BLOB,
            Types::TEXT => $fieldByName ?: new TextField($name, $name),
            
            Types::DECIMAL,
            Types::FLOAT => new DecimalField($name, $name),
            
            Types::INTEGER,
            Types::SMALLINT => new IntegerField($name, $name),
            
            Types::BOOLEAN => new ToggleBtnField($name, $name),
            
            Types::DATE_MUTABLE,
            Types::DATE_IMMUTABLE => new DateField($name, $name),
            Types::DATEINTERVAL => new DateTimeIntervalField($name, $name),
            
            Types::DATETIME_MUTABLE,
            Types::DATETIME_IMMUTABLE,
            Types::DATETIMETZ_MUTABLE,
            Types::DATETIMETZ_IMMUTABLE => new DateTimeField($name, $name),
            
            Types::JSON => new JsonField($name, $name),
            Types::SIMPLE_ARRAY => new ArrayField($name, $name),
            
            Types::TIME_MUTABLE,
            Types::TIME_IMMUTABLE => new TimeField($name, $name),
            
            default => new PureField($name, $name),
        };
    }
}