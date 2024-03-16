<?php
declare(strict_types=1);

namespace Megio\Collection;

use Doctrine\ORM\Mapping\Table;
use Megio\Database\Interface\ICrudable;

abstract class CollectionRecipe implements ICollectionRecipe
{
    public function invisibleColumns(): array
    {
        return ['id', 'updatedAt'];
    }
    
    /**
     * @param \Megio\Collection\CollectionPropType $type
     * @return \Megio\Collection\RecipeEntityMetadata
     * @throws \Megio\Collection\CollectionException
     * @throws \ReflectionException
     */
    public final function getEntityMetadata(CollectionPropType $type): RecipeEntityMetadata
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
        $metadata = new RecipeEntityMetadata($this, $rf, $type, $tableName);
        
        if (count($metadata->getSchema()['props']) === 1) {
            throw new CollectionException("Collection '{$this->name()}' has no visible columns");
        }
        
        return $metadata;
    }
}