<?php

namespace Megio\Collection;

enum CollectionPropType
{
    case READ_ONE;
    case READ_ALL;
    case NONE;
    
    /**
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @return string[]
     */
    public function getPropNames(ICollectionRecipe $recipe): array
    {
        return match ($this) {
            self::READ_ONE => array_merge(['id'], $recipe->readOne()),
            self::READ_ALL => array_merge(['id'], $recipe->readAll()),
            self::NONE => [],
        };
    }
    
    /**
     * @param array{maxLength: int|null, name: string, nullable: bool, type: string}[] $schema
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @return array{maxLength: int|null, name: string, nullable: bool, type: string}[] $schema
     */
    public function getAllowedPropNames(array $schema, ICollectionRecipe $recipe): array
    {
        $propNames = $this->getPropNames($recipe);
        $props = array_filter($schema, fn($field) => in_array($field['name'], $propNames));
        
        return array_values($props);
    }
}
