<?php
declare(strict_types=1);

namespace Megio\Collection\Mapping;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\ICollectionRecipe;
use Megio\Collection\RecipeDbSchema;
use Megio\Collection\RecipeEntityMetadata;
use Megio\Database\Interface\ICrudable;

class ArrayToEntity
{
    /** @var \Doctrine\Common\Collections\ArrayCollection<int, object> */
    private static ArrayCollection $toFlush;
    
    /**
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @param \Megio\Collection\RecipeEntityMetadata $metadata
     * @param array<string, mixed> $data
     * @return \Megio\Database\Interface\ICrudable
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public static function create(ICollectionRecipe $recipe, RecipeEntityMetadata $metadata, array $data): ICrudable
    {
        self::$toFlush = new ArrayCollection();
        
        $className = $recipe->source();
        
        /** @var \Megio\Database\Interface\ICrudable $entity */
        $entity = new $className();
        
        return self::update($metadata, $entity, $data);
    }
    
    /**
     * @param \Megio\Collection\RecipeEntityMetadata $metadata
     * @param \Megio\Database\Interface\ICrudable $entity
     * @param array<string, mixed> $data
     * @return \Megio\Database\Interface\ICrudable
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public static function update(RecipeEntityMetadata $metadata, ICrudable $entity, array $data): ICrudable
    {
        self::$toFlush = new ArrayCollection();
        
        $ref = $metadata->getReflection();
        $schema = $metadata->getFullSchemaReflectedByDoctrine();
        $methods = array_map(fn($method) => $method->name, $ref->getMethods());
        
        foreach ($data as $fieldKey => $value) {
            try {
                $methodName = 'set' . ucfirst($fieldKey);
                
                if (!in_array($methodName, $methods)) {
                    self::resolveOneToOneReverseRelation($fieldKey, $schema, $entity, $value);
                    self::resolveOneToManyReverseRelation($fieldKey, $schema, $entity, $value);
                    $ref->getProperty($fieldKey)->setValue($entity, $value);
                } else {
                    $entity->{$ref->getMethod($methodName)->name}($value);
                }
            } catch (\ReflectionException) {
                throw new CollectionException("Field '{$fieldKey}' does not exist on '{$metadata->getTableName()}' entity");
            }
        }
        
        self::addEntityToFlush($entity);
        
        return $entity;
    }
    
    protected static function resolveOneToOneReverseRelation(string $fieldKey, RecipeDbSchema $schema, ICrudable $current, mixed $value): void
    {
        $oneToOneSchemas = $schema->getOneToOneColumns();
        $oneToOneFieldNames = array_map(fn($c) => $c['name'], $oneToOneSchemas);
        
        if (in_array($fieldKey, $oneToOneFieldNames)) {
            $colSchema = $oneToOneSchemas[array_search($fieldKey, $oneToOneFieldNames)];
            $ref = new \ReflectionClass($colSchema['reverseEntity']);
            
            $currentRef = new \ReflectionClass($current);
            $currentValue = $currentRef->getProperty($fieldKey)->getValue($current);
            
            if ($currentValue !== null) {
                $targetRef = new \ReflectionClass($currentValue);
                $targetField = $oneToOneSchemas[array_search($fieldKey, $oneToOneFieldNames)]['reverseField'];
                $targetRef->getProperty($targetField)->setValue($currentValue, null);
                self::addEntityToFlush($currentValue);
            }
            
            if ($value !== null) {
                $ref->getProperty($colSchema['reverseField'])->setValue($value, $current);
                self::addEntityToFlush($value);
            }
        }
    }
    
    protected static function resolveOneToManyReverseRelation(string $fieldKey, RecipeDbSchema $schema, ICrudable $current, mixed $value): void
    {
        $oneToManySchemas = $schema->getOneToManyColumns();
        $oneToManyFieldNames = array_map(fn($c) => $c['name'], $oneToManySchemas);
        
        if (in_array($fieldKey, $oneToManyFieldNames) && $value instanceof Collection) {
            $colSchema = $oneToManySchemas[array_search($fieldKey, $oneToManyFieldNames)];
            $currentRef = new \ReflectionClass($current);
            $currentItems = $currentRef->getProperty($fieldKey)->getValue($current);
            
            foreach ($currentItems as $item) {
                if (!$value->contains($item)) {
                    $currentItems->removeElement($item);
                    $itemRef = new \ReflectionClass($item);
                    $itemRef->getProperty($colSchema['reverseField'])->setValue($item, null);
                    self::addEntityToFlush($item);
                }
            }
            
            foreach ($value as $item) {
                $colSchema = $oneToManySchemas[array_search($fieldKey, $oneToManyFieldNames)];
                $currentRef = new \ReflectionClass($colSchema['reverseEntity']);
                $currentRef->getProperty($colSchema['reverseField'])->setValue($item, $current);
                self::addEntityToFlush($item);
            }
        }
    }
    
    private static function addEntityToFlush(mixed $entity): void
    {
        if (!self::$toFlush->contains($entity)) {
            self::$toFlush->add($entity);
        }
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<int, object>
     */
    public static function getEntitiesToFlush(): ArrayCollection
    {
        return self::$toFlush;
    }
}