<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder;

use Megio\Collection\Exception\CollectionException;
use Megio\Collection\ICollectionRecipe;
use Megio\Collection\IRecipeBuilder;
use Megio\Collection\ReadBuilder\Column\Base\IColumn;
use Megio\Collection\ReadBuilder\Column\TextColumn;
use Megio\Helper\ArrayMove;

class ReadBuilder implements IRecipeBuilder
{
    protected ICollectionRecipe $recipe;
    protected ReadBuilderEvent $event;
    
    /** @var array<string, IColumn> */
    protected array $columns = [];
    
    private bool $isBuildByDbSchema = false;
    
    public function create(ICollectionRecipe $recipe, ReadBuilderEvent $event): self
    {
        $this->recipe = $recipe;
        $this->event = $event;
        return $this;
    }
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function add(IColumn $col): self
    {
        // Pokud bylo schéma vygenerováno automaticky a uživatel chce
        // přidat vlastní sloupec, tak se celé původní schéma vymaže
        if ($this->isBuildByDbSchema === true) {
            $this->columns = [];
            $this->isBuildByDbSchema = false;
        }
        
        $this->override($col->getKey(), $col);
        return $this;
    }
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function override(string $key, IColumn $col): self
    {
        if ($key !== $col->getKey()) {
            throw new CollectionException('Parameter $key must be the same as $col->getKey()');
        }
        
        $this->columns[$key] = $col;
        return $this;
    }
    
    public function build(): self
    {
        $this->appendIdColumn();
        
        return $this;
    }
    
    /**
     * @param string[] $excludedColumns
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function buildByDbSchema(array $excludedColumns = []): self
    {
        $metadata = $this->recipe->getEntityMetadata();
        $dbSchema = $metadata->getFullSchemaReflectedByDoctrine();
        
        $invisibleCols = ['id', 'createdAt', 'updatedAt'];
        $ignored = array_merge($excludedColumns, ['id']);
        
        foreach ($dbSchema as $field) {
            if (!in_array($field['name'], $ignored)) {
                $invisible = in_array($field['name'], $invisibleCols);
                $col = new TextColumn($field['name'], $field['name'], false, false, false, !$invisible);
                $this->columns[$col->getKey()] = $col;
            }
        }
        
        $this->appendIdColumn();
        
        $this->columns = ArrayMove::moveToStart($this->columns, 'id');
        $this->columns = ArrayMove::moveToEnd($this->columns, 'createdAt');
        $this->columns = ArrayMove::moveToEnd($this->columns, 'updatedAt');
        
        $this->isBuildByDbSchema = true;
        
        return $this;
    }
    
    public function countFields(): int
    {
        return count($this->columns);
    }
    
    public function getQbSelect(string $alias): string
    {
        return implode(', ', array_map(fn($col) => $alias . '.' . $col->getKey(), $this->columns));
    }
    
    public function getRecipe(): ICollectionRecipe
    {
        return $this->recipe;
    }
    
    /** @return array{
     *     renderer: string,
     *     key: string,
     *     name: string,
     *     sortable: bool,
     *     filterable: bool,
     *     searchable: bool,
     *     visible: bool
     * }[]
     */
    public function toArray(): array
    {
        $cols = array_map(fn($col) => $col->toArray(), $this->columns);
        return array_values($cols);
    }
    
    protected function appendIdColumn(): void
    {
        $this->columns = array_merge([
            'id' => new TextColumn('id', 'ID', false, false, false, false)
        ], $this->columns);
    }
}