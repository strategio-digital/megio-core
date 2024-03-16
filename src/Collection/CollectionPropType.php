<?php

namespace Megio\Collection;

enum CollectionPropType
{
    case INVISIBLE;
    case SHOW_ONE;
    case SHOW_ALL;
    case NONE;
    
    /**
     * @param \Megio\Collection\ICollectionRecipe $recipe
     * @return string[]
     */
    public function getPropNames(ICollectionRecipe $recipe): array
    {
        return match ($this) {
            self::INVISIBLE => array_merge(['id'], $recipe->invisibleColumns()),
            self::SHOW_ONE => array_merge(['id'], $recipe->showOneColumns()),
            self::SHOW_ALL => array_merge(['id'], $recipe->showAllColumns()),
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
