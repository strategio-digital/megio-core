<?php
declare(strict_types=1);

namespace Megio\Collection;

use Doctrine\ORM\Mapping\Table;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Database\Interface\ICrudable;

abstract class CollectionRecipe implements ICollectionRecipe
{
    public function read(ReadBuilder $builder, RecipeRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema();
    }
    
    public function readAll(ReadBuilder $builder, RecipeRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema();
    }
    
    public function create(WriteBuilder $builder, RecipeRequest $request): WriteBuilder
    {
        return $builder->buildByDbSchema();
    }
    
    public function update(WriteBuilder $builder, RecipeRequest $request): WriteBuilder
    {
        return $builder->buildByDbSchema();
    }
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public final function getEntityMetadata(): RecipeEntityMetadata
    {
        if (!is_subclass_of($this->source(), ICrudable::class)) {
            throw new CollectionException("Entity '{$this->source()}' does not implement ICrudable");
        }
        
        $rf = new \ReflectionClass($this->source());
        $attr = $rf->getAttributes(Table::class);
        
        if (count($attr) === 0) {
            throw new CollectionException("Entity '{$this->source()}' is missing Table attribute");
        }
        
        /** @var Table $attrInstance */
        $attrInstance = $attr[0]->newInstance();
        
        if ($attrInstance->name === null) {
            throw new CollectionException("Entity '{$this->source()}' has Table attribute without name");
        }
        
        $tableName = str_replace('`', '', $attrInstance->name);
        
        return new RecipeEntityMetadata($this, $rf, $tableName);
    }
}