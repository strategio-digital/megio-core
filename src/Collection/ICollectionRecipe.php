<?php
declare(strict_types=1);

namespace Megio\Collection;

use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\ReadBuilder\ReadBuilder;

interface ICollectionRecipe
{
    /** @return class-string */
    public function source(): string;
    
    /** @return string */
    public function name(): string;
    
    /** @return string[] */
    public function invisible(): array;
    
    /** @return string[] */
    public function showOne(): array;
    
    /** @return string[] */
    public function showAll(): array;
    
    public function readOne(ReadBuilder $builder): ReadBuilder;
    
    public function create(WriteBuilder $builder): WriteBuilder;
    
    public function update(WriteBuilder $builder): WriteBuilder;
    
    /**
     * @throws \Megio\Collection\CollectionException
     * @throws \ReflectionException
     */
    public function getEntityMetadata(CollectionPropType $type): RecipeEntityMetadata;
}