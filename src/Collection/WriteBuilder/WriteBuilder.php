<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder;

use Doctrine\ORM\EntityManagerInterface;
use Megio\Collection\Helper\FieldCreator;
use Megio\Collection\Helper\RuleCreator;
use Megio\Collection\IRecipeBuilder;
use Megio\Collection\RecipeDbSchema;
use Megio\Collection\WriteBuilder\Field\Base\EmptyValue;
use Megio\Collection\WriteBuilder\Field\Base\IField;
use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Megio\Collection\WriteBuilder\Field\ToManySelectField;
use Megio\Collection\WriteBuilder\Field\ToOneSelectField;
use Megio\Collection\WriteBuilder\Rule\NullableRule;
use Megio\Collection\WriteBuilder\Rule\RequiredRule;
use Megio\Collection\ICollectionRecipe;
use Megio\Collection\RecipeEntityMetadata;
use Megio\Helper\ArrayMove;

class WriteBuilder implements IRecipeBuilder
{
    private WriteBuilderEvent $event;
    
    private ICollectionRecipe $recipe;
    
    private RecipeEntityMetadata $metadata;
    
    private RecipeDbSchema $dbSchema;
    
    private ?string $rowId = null;
    
    /** @var array<string, IField> */
    private array $fields = [];
    
    /** @var array<string, string[]> */
    private array $errors = [];
    
    
    /** @var array<string, string|int|float|bool|null> */
    private array $values = [];
    
    /** @var array<string, class-string[]> */
    private array $ignoredRules = [];
    
    private bool $keepDbSchema = true;
    
    public function __construct(protected readonly EntityManagerInterface $em)
    {
    }
    
    public function reset(): self
    {
        $this->rowId = null;
        $this->fields = [];
        $this->errors = [];
        $this->values = [];
        $this->ignoredRules = [];
        $this->keepDbSchema = true;
        
        return $this;
    }
    
    /**
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @param \Megio\Collection\WriteBuilder\WriteBuilderEvent $event
     * @param array<string, string|int|float|bool|null> $values
     * @param string|null $rowId
     * @return $this
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function create(ICollectionRecipe $recipe, WriteBuilderEvent $event, string $rowId = null, array $values = []): self
    {
        $this->reset();
        
        $this->recipe = $recipe;
        $this->values = $values;
        $this->event = $event;
        $this->rowId = $rowId;
        
        $this->metadata = $recipe->getEntityMetadata();
        $this->dbSchema = $this->metadata->getFullSchemaReflectedByDoctrine();
        
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
    
    public function build(): self
    {
        $this->createValues();
        return $this;
    }
    
    /**
     * @param string[] $exclude
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function buildByDbSchema(array $exclude = [], bool $persist = false): self
    {
        $ignored = array_merge($exclude, ['id']);
        
        $unionColumns = array_filter($this->dbSchema->getUnionColumns(), fn($cs) => !in_array($cs['name'], $ignored));
        foreach ($unionColumns as $cs) {
            $field = FieldCreator::create($this, $cs['type'], $cs['name'], $cs['defaultValue']);
            $field = RuleCreator::createRulesByDbSchema($field, $this->recipe, $cs);
            $this->fields[$field->getName()] = $field;
        }
        
        $oneToOneColumns = array_filter($this->dbSchema->getOneToOneColumns(), fn($cs) => !in_array($cs['name'], $ignored));
        foreach ($oneToOneColumns as $cs) {
            $field = new ToOneSelectField($cs['name'], $cs['name'], $cs['reverseEntity']);
            if ($cs['nullable']) {
                $field->addRule(new NullableRule());
            }
            $field->setBuilder($this);
            $this->fields[$field->getName()] = $field;
        }
        
        $oneToOneMany = array_filter($this->dbSchema->getOneToManyColumns(), fn($cs) => !in_array($cs['name'], $ignored));
        foreach ($oneToOneMany as $cs) {
            $field = new ToManySelectField($cs['name'], $cs['name'], $cs['reverseEntity']);
            $field->setBuilder($this);
            $this->fields[$field->getName()] = $field;
        }
        
        $manyToOne = array_filter($this->dbSchema->getManyToOneColumns(), fn($cs) => !in_array($cs['name'], $ignored));
        foreach ($manyToOne as $cs) {
            $field = new ToOneSelectField($cs['name'], $cs['name'], $cs['reverseEntity']);
            if ($cs['nullable']) {
                $field->addRule(new NullableRule());
            }
            $field->setBuilder($this);
            $this->fields[$field->getName()] = $field;
        }
        
        $manyToMany = array_filter($this->dbSchema->getManyToManyColumns(), fn($cs) => !in_array($cs['name'], $ignored));
        foreach ($manyToMany as $cs) {
            $field = new ToManySelectField($cs['name'], $cs['name'], $cs['reverseEntity']);
            $field->setBuilder($this);
            $this->fields[$field->getName()] = $field;
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
    
    /** @return array<string, class-string[]> */
    public function getIgnoredRules(): array
    {
        return $this->ignoredRules;
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
    
    public function countFields(): int
    {
        return count($this->fields);
    }
    
    public function isValid(): bool
    {
        return array_reduce($this->errors, fn($sum, $items) => $sum + count($items), 0) === 0;
    }
    
    /**
     * @return array<int, mixed>
     */
    public function toArray(): array
    {
        foreach ($this->fields as $field) {
            $formatters = $field->getFormatters();
            foreach ($formatters as $formatter) {
                $formatter->setBuilder($this);
                $value = $formatter->format($field->getValue(), $field->getName());
                $field->setValue($value);
            }
        }
        
        $fields = array_values(array_map(fn($field) => $field->toArray(), $this->fields));
        
        foreach ($fields as $key => $field) {
            if ($field['default_value'] instanceof UndefinedValue) {
                unset($fields[$key]['default_value']);
            }
            
            if ($field['value'] instanceof UndefinedValue || $field['value'] instanceof EmptyValue) {
                unset($fields[$key]['value']);
            }
            
        }
        
        return $fields;
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
                if ($field->getValue() instanceof UndefinedValue === false && $field->getValue() instanceof EmptyValue === false) {
                    foreach ($field->getSerializers() as $serializer) {
                        $serializer->setBuilder($this);
                        $serialized = $serializer->serialize($field);
                        $field->setValue($serialized);
                    }
                    $values[$field->getName()] = $field->getValue();
                }
            }
        }
        
        return $values;
    }
    
    private function recreateRules(IField $field): IField
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
    
    private function createValues(): void
    {
        foreach ($this->fields as $key => $field) {
            $this->fields[$key] = $this->recreateRules($field);
            if (array_key_exists($field->getName(), $this->values) && $field->getValue() instanceof UndefinedValue) {
                $field->setValue($this->values[$field->getName()]);
            }
        }
    }
}