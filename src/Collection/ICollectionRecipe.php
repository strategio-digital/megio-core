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
    public function read(ReadBuilder $builder, Request $request): ReadBuilder;
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function readAll(ReadBuilder $builder, Request $request): ReadBuilder;
    
    public function create(WriteBuilder $builder, Request $request): WriteBuilder;
    
    public function update(WriteBuilder $builder, Request $request): WriteBuilder;
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function getEntityMetadata(): RecipeEntityMetadata;
}