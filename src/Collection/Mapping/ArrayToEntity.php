<?php
declare(strict_types=1);

namespace Megio\Collection\Mapping;

use Megio\Collection\Exception\CollectionException;
use Megio\Collection\ICollectionRecipe;
use Megio\Collection\RecipeDbSchema;
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
    public static function create(ICollectionRecipe $recipe, RecipeEntityMetadata $metadata, array $data): ICrudable
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
        $schema = $metadata->getFullSchemaReflectedByDoctrine();
        $methods = array_map(fn($method) => $method->name, $ref->getMethods());
        
        foreach ($data as $fieldKey => $value) {
            try {
                $methodName = 'set' . ucfirst($fieldKey);
                if (!in_array($methodName, $methods)) {
                    self::resolveOneToOne($fieldKey, $schema, $entity, $value);
                    $ref->getProperty($fieldKey)->setValue($entity, $value);
                } else {
                    $entity->{$ref->getMethod($methodName)->name}($value);
                }
            } catch (\ReflectionException) {
                throw new CollectionException("Field '{$fieldKey}' does not exist on '{$metadata->getTableName()}' entity");
            }
        }
        
        return $entity;
    }
    
    protected static function resolveOneToOne(string $fieldKey, RecipeDbSchema $schema, ICrudable $current, mixed $value): void
    {
        $oneToOneSchemas = $schema->getOneToOneColumns();
        $oneToOneFieldNames = array_map(fn($c) => $c['name'], $oneToOneSchemas);
        
        if (in_array($fieldKey, $oneToOneFieldNames)) {
            $colSchema = $oneToOneSchemas[array_search($fieldKey, $oneToOneFieldNames)];
            $ref = new \ReflectionClass($colSchema['reverseEntity']);
            $ref->getProperty($colSchema['reverseField'])->setValue($value, $current);
        }
    }
}