<?php
declare(strict_types=1);

namespace Megio\Collection;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Megio\Database\Interface\ICrudable;
use ReflectionClass;

class RecipeEntityMetadata
{
    /**
     * @param ReflectionClass<ICrudable> $entityRef
     */
    public function __construct(
        protected ICollectionRecipe $recipe,
        protected ReflectionClass  $entityRef,
        protected string            $tableName,
    ) {}

    public function getRecipe(): ICollectionRecipe
    {
        return $this->recipe;
    }

    /**
     * @return ReflectionClass<ICrudable>
     */
    public function getReflection(): ReflectionClass
    {
        return $this->entityRef;
    }

    /**
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getFullSchemaReflectedByDoctrine(): RecipeDbSchema
    {
        $schema = new RecipeDbSchema();

        foreach ($this->entityRef->getProperties() as $prop) {
            $attrs = array_map(fn($attr) => $attr->newInstance(), $prop->getAttributes());

            $columnAttrs = array_filter($attrs, fn($attr) => $attr instanceof Column);
            if (count($columnAttrs) !== 0) {
                $attr = array_values($columnAttrs)[0];
                $schema->addUnionColumn($attr, $prop);
            }

            $oneToOneAttrs = array_filter($attrs, fn($attr) => $attr instanceof OneToOne);
            if (count($oneToOneAttrs) !== 0) {
                $attr = array_values($oneToOneAttrs)[0];
                $schema->addOneToOneColumn($attr, $prop);
            }

            $oneToManyAttrs = array_filter($attrs, fn($attr) => $attr instanceof OneToMany);
            if (count($oneToManyAttrs) !== 0) {
                $attr = array_values($oneToManyAttrs)[0];
                $schema->addOneToManyColumn($attr, $prop);
            }

            $manyToOneAttrs = array_filter($attrs, fn($attr) => $attr instanceof ManyToOne);
            if (count($manyToOneAttrs) !== 0) {
                $attr = array_values($manyToOneAttrs)[0];
                $schema->addManyToOneColumn($attr, $prop);
            }

            $manyToManyAttrs = array_filter($attrs, fn($attr) => $attr instanceof ManyToMany);
            if (count($manyToManyAttrs) !== 0) {
                $attr = array_values($manyToManyAttrs)[0];
                $schema->addManyToManyColumn($attr, $prop);
            }
        }

        return $schema;
    }
}
