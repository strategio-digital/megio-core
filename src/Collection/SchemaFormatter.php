<?php
declare(strict_types=1);

namespace Megio\Collection;

class SchemaFormatter
{
    /**
     * @return array{
     *     recipe: array{ key: string, name: string },
     *     props: array<int, mixed>
     * }
     */
    public static function format(ICollectionRecipe $recipe, IRecipeBuilder $builder): array
    {
        return [
            'recipe' => [
                'key' => $recipe->key(),
                'name' => $recipe->name(),
            ],
            'props' => $builder->toArray()
        ];
    }
}