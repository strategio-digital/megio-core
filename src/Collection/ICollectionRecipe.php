<?php
declare(strict_types=1);

namespace Megio\Collection;

use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Symfony\Component\HttpFoundation\Request;

interface ICollectionRecipe
{
    /** @return class-string */
    public function source(): string;
    
    /** @return string */
    public function key(): string;
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function read(ReadBuilder $builder, RecipeRequest $request): ReadBuilder;
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function readAll(ReadBuilder $builder, RecipeRequest $request): ReadBuilder;
    
    public function create(WriteBuilder $builder, RecipeRequest $request): WriteBuilder;
    
    public function update(WriteBuilder $builder, RecipeRequest $request): WriteBuilder;
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function getEntityMetadata(): RecipeEntityMetadata;
}