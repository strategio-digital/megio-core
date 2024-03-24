<?php
declare(strict_types=1);

namespace Megio\Collection;

class SchemaFormatter
{
    /**
     * @return array{meta: array{recipe: string}, props: array<int, mixed>}>
     */
    public static function format(ICollectionRecipe $recipe, IRecipeBuilder $builder): array
    {
        return [
            'meta' => [
                'recipe' => $recipe->key()
            ],
            'props' => $builder->toArray()
        ];
    }
}