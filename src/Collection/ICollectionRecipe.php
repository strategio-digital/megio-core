<?php
declare(strict_types=1);

namespace Megio\Collection;

use Megio\Collection\Builder\Builder;

interface ICollectionRecipe
{
    /** @return class-string */
    public function source(): string;
    
    /** @return string */
    public function name(): string;
    
    
    /** @return string[] */
    public function invisible(): array;
    
    /** @return string[] */
    public function readOne(): array;
    
    /** @return string[] */
    public function readAll(): array;
    
    
    public function create(Builder $builder): Builder;
    
    public function update(Builder $builder): Builder;
    
    /**
     * @throws \Megio\Collection\CollectionException
     * @throws \ReflectionException
     */
    public function getEntityMetadata(CollectionPropType $type): RecipeEntityMetadata;
}