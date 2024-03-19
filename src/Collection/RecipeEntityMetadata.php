<?php
declare(strict_types=1);

namespace Megio\Collection;

use Doctrine\ORM\Mapping\Column;

readonly class RecipeEntityMetadata
{
    /**
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @param \ReflectionClass<\Megio\Database\Interface\ICrudable> $entityRef
     * @param \Megio\Collection\CollectionPropType $type
     * @param string $tableName
     */
    public function __construct(
        private ICollectionRecipe  $recipe,
        private \ReflectionClass   $entityRef,
        private CollectionPropType $type,
        private string             $tableName,
    )
    {
    }
    
    public function getRecipe(): ICollectionRecipe
    {
        return $this->recipe;
    }
    
    /**
     * @return \ReflectionClass<\Megio\Database\Interface\ICrudable>
     */
    public function getReflection(): \ReflectionClass
    {
        return $this->entityRef;
    }
    
    public function getType(): CollectionPropType
    {
        return $this->type;
    }
    
    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }
    
    public function getQbSelect(string $qbAlias): string
    {
        $schema = $this->getFullSchemaReflectedByDoctrine();
        $visibleProps = $this->type->getAllowedPropNames($schema, $this->recipe);
        return implode(', ', array_map(fn($col) => $qbAlias . '.' . $col['name'], $visibleProps));
    }
    
    /**
     * @return array{
     *     meta: array{recipe: string, invisible: string[]},
     *     props: array{maxLength: int|null, name: string, nullable: bool, type: string}[]
     * }
     */
    public function getSchema(): array
    {
        $schema = $this->getFullSchemaReflectedByDoctrine();
        $props = $this->type->getAllowedPropNames($schema, $this->recipe);
        
        return [
            'meta' => [
                'recipe' => $this->recipe->name(),
                'invisible' => $this->recipe->invisible()
            ],
            'props' => $this->sortColumns($props)
        ];
    }
    
    /**
     * @return array{name: string, type: string, unique: bool, nullable: bool, maxLength: int|null}[]
     */
    public function getFullSchemaReflectedByDoctrine(): array
    {
        $props = [];
        foreach ($this->entityRef->getProperties() as $prop) {
            $attrs = array_map(fn($attr) => $attr->newInstance(), $prop->getAttributes());
            
            /** @var Column[] $columnAttrs */
            $columnAttrs = array_filter($attrs, fn($attr) => $attr instanceof Column);
            if (count($columnAttrs) !== 0) {
                $attr = array_values($columnAttrs)[0];
                $props[] = $this->getColumnMetadata($attr, $prop);
            }
        }
        
        // move array item with name "id" to first position
        $idProp = array_filter($props, fn($prop) => $prop['name'] !== 'id');
        return array_merge(array_values(array_filter($props, fn($prop) => $prop['name'] === 'id')), $idProp);
    }
    
    /**
     * @param \Doctrine\ORM\Mapping\Column $attr
     * @param \ReflectionProperty $prop
     * @return array{name: string, type: string, unique: bool, nullable: bool, maxLength: int|null}
     */
    public function getColumnMetadata(Column $attr, \ReflectionProperty $prop): array
    {
        $propType = $prop->getType();
        $nullable = $attr->nullable;
        
        $type = $attr->type;
        if ($type === null) {
            $type = $propType instanceof \ReflectionNamedType ? $propType->getName() : $propType ?? '@unknown';
        }
        
        $maxLength = $attr->length;
        if ($maxLength === null && $type === 'string') {
            $maxLength = 255;
        }
        
        return [
            'name' => $prop->getName(),
            'type' => mb_strtolower($type),
            'unique' => $attr->unique,
            'nullable' => $nullable,
            'maxLength' => $maxLength
        ];
    }
    
    
    /**
     * @param array{maxLength: int|null, name: string, nullable: bool, type: string}[] $fields
     * @return array{maxLength: int|null, name: string, nullable: bool, type: string}[]
     */
    public function sortColumns(array $fields): array
    {
        $associativeFields = [];
        foreach ($fields as $field) {
            $associativeFields[$field['name']] = $field;
        }
        
        $sortedFields = [];
        foreach ($this->type->getPropNames($this->recipe) as $name) {
            if (array_key_exists($name, $associativeFields)) {
                $sortedFields[] = $associativeFields[$name];
            }
        }
        
        return $sortedFields;
    }
}