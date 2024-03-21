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
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function read(ReadBuilder $builder): ReadBuilder;
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function readAll(ReadBuilder $builder): ReadBuilder;
    
    public function create(WriteBuilder $builder): WriteBuilder;
    
    public function update(WriteBuilder $builder): WriteBuilder;
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function getEntityMetadata(): RecipeEntityMetadata;
}