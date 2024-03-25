<?php
declare(strict_types=1);

namespace Megio\Collection\Mapping;

use Megio\Collection\Exception\CollectionException;
use Megio\Collection\ICollectionRecipe;
use Megio\Collection\RecipeEntityMetadata;
use Megio\Database\Interface\ICrudable;

class ArrayToEntity
{
    /**
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @param \Megio\Collection\RecipeEntityMetadata $metadata
     * @param array<string, mixed> $data
     * @return \Megio\Database\Interface\ICrudable
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public static function create(
        ICollectionRecipe    $recipe,
        RecipeEntityMetadata $metadata,
        array                $data
    ): ICrudable
    {
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
        $ref = $metadata->getReflection();
        $methods = array_map(fn($method) => $method->name, $ref->getMethods());
        
        foreach ($data as $key => $value) {
            try {
                $methodName = 'set' . ucfirst($key);
                if (in_array($methodName, $methods)) {
                    $m = $ref->getMethod($methodName)->name;
                    $entity->$m($value);
                } else {
                    $ref->getProperty($key)->setValue($entity, $value);
                }
            } catch (\ReflectionException) {
                throw new CollectionException("Field '{$key}' does not exist on '{$metadata->getTableName()}' entity");
            }
        }
        
        return $entity;
    }
}