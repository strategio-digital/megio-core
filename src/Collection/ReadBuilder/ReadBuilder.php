<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Megio\Collection\Helper\ColumnCreator;
use Megio\Collection\ICollectionRecipe;
use Megio\Collection\IRecipeBuilder;
use Megio\Collection\ReadBuilder\Column\Base\ShowOnlyOn;
use Megio\Collection\ReadBuilder\Column\Base\IColumn;
use Megio\Collection\ReadBuilder\Column\OneToOneEntityColumn;
use Megio\Collection\ReadBuilder\Column\StringColumn;
use Megio\Collection\RecipeDbSchema;
use Megio\Collection\RecipeEntityMetadata;
use Megio\Helper\ArrayMove;

class ReadBuilder implements IRecipeBuilder
{
    private ICollectionRecipe $recipe;
    
    private ReadBuilderEvent $event;
    
    private RecipeEntityMetadata $metadata;
    
    private RecipeDbSchema $dbSchema;
    
    /** @var array<string, IColumn> */
    private array $columns = [];
    
    /** @var array<string, class-string[]> */
    private array $ignoredFormatters = [];
    
    private bool $keepDbSchema = true;
    
    /**
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function create(ICollectionRecipe $recipe, ReadBuilderEvent $event): self
    {
        $this->recipe = $recipe;
        $this->event = $event;
        
        $this->metadata = $this->recipe->getEntityMetadata();
        $this->dbSchema = $this->metadata->getFullSchemaReflectedByDoctrine();
        return $this;
    }
    
    public function add(IColumn $col, string $moveBeforeKey = null, string $moveAfterKey = null): self
    {
        $this->addIdColumnIfNotExists();
        
        if ($this->keepDbSchema === false) {
            $this->columns = [];
            $this->keepDbSchema = true;
        }
        
        $this->columns[$col->getKey()] = $col;
        
        if ($moveBeforeKey !== null) {
            $this->columns = ArrayMove::moveBefore($this->columns, $col->getKey(), $moveBeforeKey);
        }
        
        if ($moveAfterKey !== null) {
            $this->columns = ArrayMove::moveAfter($this->columns, $col->getKey(), $moveAfterKey);
        }
        
        return $this;
    }
    
    public function build(): self
    {
        $this->addIdColumnIfNotExists();
        
        return $this;
    }
    
    /**
     * @param string[] $exclude
     */
    public function buildByDbSchema(array $exclude = [], bool $persist = false): self
    {
        $this->addIdColumnIfNotExists();
        
        $invisibleCols = ['id', 'createdAt', 'updatedAt'];
        $ignored = array_merge($exclude, ['id']);
        
        foreach ($this->dbSchema->getUnionColumns() as $field) {
            if (!in_array($field['name'], $ignored)) {
                $visible = !in_array($field['name'], $invisibleCols);
                $col = ColumnCreator::create($field['type'], $field['name'], $visible);
                $this->columns[$col->getKey()] = $col;
            }
        }
        
        foreach ($this->dbSchema->getOneToOneColumns() as $field) {
            if (!in_array($field['name'], $ignored)) {
                $visible = !in_array($field['name'], $invisibleCols);
                $col = new OneToOneEntityColumn(key: $field['name'], name: $field['name'], visible: $visible);
                $this->columns[$col->getKey()] = $col;
            }
        }
        
        $this->columns = ArrayMove::moveToStart($this->columns, 'id');
        $this->columns = ArrayMove::moveToEnd($this->columns, 'createdAt');
        $this->columns = ArrayMove::moveToEnd($this->columns, 'updatedAt');
        
        $this->keepDbSchema = $persist;
        
        return $this;
    }
    
    /**
     * @return array<string, mixed>
     * @throws \ReflectionException
     */
    public function format(mixed $values, bool $isAdminPanel): array
    {
        $result = [];
        foreach ($this->columns as $col) {
            $key = $col->getKey();
            $formatters = $col->getFormatters();
            
            $ignoredFormatters = array_key_exists($key, $this->ignoredFormatters)
                ? $this->ignoredFormatters[$key]
                : [];
            
            $ref = new \ReflectionClass($values);
            $result[$key] = $ref->getProperty($key)->getValue($values);
            
            foreach ($formatters as $formatter) {
                if (
                    !in_array($formatter::class, $ignoredFormatters)
                    && (
                        $formatter->showOnlyOn() === null
                        || ($isAdminPanel && $formatter->showOnlyOn() === ShowOnlyOn::ADMIN_PANEL)
                        || (!$isAdminPanel && $formatter->showOnlyOn() === ShowOnlyOn::API)
                    )
                ) {
                    $result[$key] = $formatter->format($result[$key]);
                }
                unset($formatter); // Just performance optimization
            }
        }
        
        return $result;
    }
    
    /**
     * @param array<string, class-string[]> $formatters
     */
    public function ignoreFormatters(array $formatters): self
    {
        $this->ignoredFormatters = $formatters;
        return $this;
    }
    
    public function countFields(): int
    {
        return count($this->columns);
    }
    
    /**
     * @param \Doctrine\ORM\EntityRepository<object> $repo
     * @param string $alias
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilder(EntityRepository $repo, string $alias): QueryBuilder
    {
        $qb = $repo
            ->createQueryBuilder($alias)
            ->select($alias);
        
        foreach ($this->dbSchema->getOneToOneColumns() as $column) {
            $qb->addSelect($column['name']);
            $qb->leftJoin("{$alias}.{$column['name']}", $column['name']);
        }
        
        return $qb;
    }
    
    public function getRecipe(): ICollectionRecipe
    {
        return $this->recipe;
    }
    
    public function getEvent(): ReadBuilderEvent
    {
        return $this->event;
    }
    
    /** @return array{
     *     renderer: string,
     *     key: string,
     *     name: string,
     *     sortable: bool,
     *     visible: bool,
     *     formatters: class-string[]
     * }[]
     */
    public function toArray(): array
    {
        $cols = array_map(fn($col) => $col->toArray(), $this->columns);
        return array_values($cols);
    }
    
    public function dump(): void
    {
        dumpe($this->build()->toArray());
    }
    
    protected function addIdColumnIfNotExists(): void
    {
        if (!array_key_exists('id', $this->columns)) {
            $this->columns = array_merge([
                'id' => new StringColumn(key: 'id', name: 'ID', visible: false),
            ], $this->columns);
        }
    }
}