<?php
declare(strict_types=1);

namespace Megio\Collection;

use Megio\Collection\FieldBuilder\FieldBuilder;

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
    
    
    public function create(FieldBuilder $builder): FieldBuilder;
    
    public function update(FieldBuilder $builder): FieldBuilder;
    
    /**
     * @throws \Megio\Collection\CollectionException
     * @throws \ReflectionException
     */
    public function getEntityMetadata(CollectionPropType $type): RecipeEntityMetadata;
}