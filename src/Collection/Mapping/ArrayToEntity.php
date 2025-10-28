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
use ReflectionClass;
use ReflectionException;

class ArrayToEntity
{
    /** @var ArrayCollection<int, object> */
    private static ArrayCollection $toFlush;

    /**
     * @param array<string, mixed> $data
     *
     * @throws CollectionException
     */
    public static function create(ICollectionRecipe $recipe, RecipeEntityMetadata $metadata, array $data): ICrudable
    {
        self::$toFlush = new ArrayCollection();

        $className = $recipe->source();

        /** @var ICrudable $entity */
        $entity = new $className();

        return self::update($metadata, $entity, $data);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws CollectionException
     */
    public static function update(RecipeEntityMetadata $metadata, ICrudable $entity, array $data): ICrudable
    {
        self::$toFlush = new ArrayCollection();

        $ref = $metadata->getReflection();
        $schema = $metadata->getFullSchemaReflectedByDoctrine();
        $methods = array_map(fn($method) => $method->name, $ref->getMethods());

        foreach ($data as $fieldKey => $value) {
            try {
                // Example: 'order_' property method have probably 'setOrder()' not 'setOrder_()'
                $methodName = 'set' . str_replace('_', '', ucwords($fieldKey, '_'));

                if (!in_array($methodName, $methods, true)) {
                    self::resolveOneToOneReverseRelation($fieldKey, $schema, $entity, $value);
                    self::resolveOneToManyReverseRelation($fieldKey, $schema, $entity, $value);
                    self::resolveManyToManyReverseRelation($fieldKey, $schema, $entity, $value);
                    $ref->getProperty($fieldKey)->setValue($entity, $value);
                } else {
                    $ref->getMethod($methodName)->invoke($entity, $value);
                    //$entity->{$ref->getMethod($methodName)->name}($value);
                }
            } catch (ReflectionException) {
                throw new CollectionException("Field '{$fieldKey}' does not exist on '{$metadata->getTableName()}' entity");
            }
        }

        self::addEntityToFlush($entity);

        return $entity;
    }

    protected static function resolveOneToOneReverseRelation(string $fieldKey, RecipeDbSchema $schema, ICrudable $current, mixed $value): void
    {
        $schemas = $schema->getOneToOneColumns();
        $schemaNames = array_map(fn($c) => $c['name'], $schemas);

        if (in_array($fieldKey, $schemaNames, true)) {
            $colSchema = $schemas[array_search($fieldKey, $schemaNames, true)];
            $ref = new ReflectionClass($colSchema['reverseEntity']);

            $currentRef = new ReflectionClass($current);
            $currentValue = $currentRef->getProperty($fieldKey)->getValue($current);

            if ($currentValue !== null) {
                $targetRef = new ReflectionClass($currentValue);
                $targetField = $schemas[array_search($fieldKey, $schemaNames, true)]['reverseField'];
                if ($targetField !== null) {
                    $targetRef->getProperty($targetField)->setValue($currentValue, null);
                }
                self::addEntityToFlush($currentValue);
            }

            if ($value !== null) {
                if ($colSchema['reverseField'] !== null) {
                    $ref->getProperty($colSchema['reverseField'])->setValue($value, $current);
                }
                self::addEntityToFlush($value);
            }
        }
    }

    protected static function resolveOneToManyReverseRelation(string $fieldKey, RecipeDbSchema $schema, ICrudable $current, mixed $value): void
    {
        $schemas = $schema->getOneToManyColumns();
        $schemaNames = array_map(fn($c) => $c['name'], $schemas);

        if (in_array($fieldKey, $schemaNames, true) && $value instanceof Collection) {
            $colSchema = $schemas[array_search($fieldKey, $schemaNames, true)];
            $currentRef = new ReflectionClass($current);
            $currentItems = $currentRef->getProperty($fieldKey)->getValue($current);

            foreach ($currentItems as $item) {
                if (!$value->contains($item)) {
                    $currentItems->removeElement($item);
                    if ($colSchema['reverseField'] !== null) {
                        $itemRef = new ReflectionClass($item);
                        $itemRef->getProperty($colSchema['reverseField'])->setValue($item, null);
                    }
                    self::addEntityToFlush($item);
                }
            }

            foreach ($value as $item) {
                $colSchema = $schemas[array_search($fieldKey, $schemaNames, true)];
                if ($colSchema['reverseField'] !== null) {
                    $currentRef = new ReflectionClass($colSchema['reverseEntity']);
                    $currentRef->getProperty($colSchema['reverseField'])->setValue($item, $current);
                }
                self::addEntityToFlush($item);
            }
        }
    }

    protected static function resolveManyToManyReverseRelation(string $fieldKey, RecipeDbSchema $schema, ICrudable $current, mixed $value): void
    {
        $schemas = $schema->getManyToManyColumns();
        $schemaNames = array_map(fn($c) => $c['name'], $schemas);

        if (in_array($fieldKey, $schemaNames, true) && $value instanceof Collection) {
            $colSchema = $schemas[array_search($fieldKey, $schemaNames, true)];
            $currentRef = new ReflectionClass($current);
            $currentProp = $currentRef->getProperty($fieldKey);
            $currentItems = $currentProp->getValue($current);

            foreach ($value as $item) {
                if ($colSchema['reverseField'] !== null) {
                    $itemRef = new ReflectionClass($item);
                    $collection = $itemRef->getProperty($colSchema['reverseField'])->getValue($item);
                    if (!$currentItems->contains($current) && !$collection->contains($current)) {
                        $collection->add($current);
                        self::addEntityToFlush($item);
                    }
                }
            }

            foreach ($currentItems as $item) {
                if (!$value->contains($item)) {
                    if ($colSchema['reverseField'] !== null) {
                        $itemRef = new ReflectionClass($item);
                        $collection = $itemRef->getProperty($colSchema['reverseField'])->getValue($item);
                        $collection->removeElement($current);
                    }
                    self::addEntityToFlush($item);
                }
            }
        }
    }

    private static function addEntityToFlush(mixed $entity): void
    {
        if (!self::$toFlush->contains($entity)) {
            self::$toFlush->add($entity);
        }
    }
}
