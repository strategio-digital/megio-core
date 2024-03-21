<?php
declare(strict_types=1);

namespace Megio\Collection;

use Doctrine\ORM\Mapping\Column;

class RecipeEntityMetadata
{
    /**
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @param \ReflectionClass<\Megio\Database\Interface\ICrudable> $entityRef
     * @param string $tableName
     */
    public function __construct(
        protected ICollectionRecipe $recipe,
        protected \ReflectionClass  $entityRef,
        protected string            $tableName,
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
    
    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
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
        
        /**
         * Doctrine mapping:
         * https://www.doctrine-project.org/projects/doctrine-orm/en/3.1/reference/basic-mapping.html#doctrine-mapping-types
         * @see \Megio\Collection\ReadBuilder\ReadBuilder::createColumnInstance()
         * @see \Megio\Collection\WriteBuilder\WriteBuilder::createRuleInstance()
         */
        $type = $attr->type;
        
        // Fallback to union types
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
}