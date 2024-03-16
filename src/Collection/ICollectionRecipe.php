<?php
declare(strict_types=1);

namespace Megio\Collection;

interface ICollectionRecipe
{
    /** @return class-string */
    public function source(): string;
    
    /** @return string */
    public function name(): string;
    
    /** @return string[] */
    public function invisibleColumns(): array;
    
    /** @return string[] */
    public function showOneColumns(): array;
    
    /** @return string[] */
    public function showAllColumns(): array;
    
    /**
     * @throws \Megio\Collection\CollectionException
     * @throws \ReflectionException
     */
    public function getEntityMetadata(CollectionPropType $type): RecipeEntityMetadata;
}