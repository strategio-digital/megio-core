<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\Helper\ColumnCreator;
use Megio\Collection\ICollectionRecipe;
use Megio\Collection\IRecipeBuilder;
use Megio\Collection\ReadBuilder\Column\Base\IColumn;
use Megio\Collection\ReadBuilder\Column\Base\ShowOnlyOn;
use Megio\Collection\ReadBuilder\Column\StringColumn;
use Megio\Collection\ReadBuilder\Column\ToManyColumn;
use Megio\Collection\ReadBuilder\Column\ToOneColumn;
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

    public function reset(): self
    {
        $this->columns = [];
        $this->ignoredFormatters = [];
        $this->keepDbSchema = true;

        return $this;
    }

    /**
     * @throws CollectionException
     */
    public function create(ICollectionRecipe $recipe, ReadBuilderEvent $event): self
    {
        $this->reset();

        $this->recipe = $recipe;
        $this->event = $event;

        $this->metadata = $this->recipe->getEntityMetadata();
        $this->dbSchema = $this->metadata->getFullSchemaReflectedByDoctrine();
        return $this;
    }

    public function add(IColumn $col, ?string $moveBeforeKey = null, ?string $moveAfterKey = null): self
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

        $sortableCols = ['id', 'createdAt', 'updatedAt'];
        $invisibleCols = ['id', 'createdAt', 'updatedAt'];
        $ignored = array_merge($exclude, ['id']);

        foreach ($this->dbSchema->getUnionColumns() as $column) {
            if (!in_array($column['name'], $ignored, true)) {
                $visible = !in_array($column['name'], $invisibleCols, true);
                $col = ColumnCreator::create($column['type'], $column['name'], $visible, in_array($column['name'], $sortableCols, true));
                $this->columns[$col->getKey()] = $col;
            }
        }

        $toOneColumns = array_merge($this->dbSchema->getOneToOneColumns(), $this->dbSchema->getManyToOneColumns());
        foreach ($toOneColumns as $column) {
            if (!in_array($column['name'], $ignored, true)) {
                $visible = !in_array($column['name'], $invisibleCols, true);
                $col = new ToOneColumn(key: $column['name'], name: $column['name'], visible: $visible);
                $this->columns[$col->getKey()] = $col;
            }
        }

        $toManyColumns = array_merge($this->dbSchema->getOneToManyColumns(), $this->dbSchema->getManyToManyColumns());
        foreach ($toManyColumns as $column) {
            if (!in_array($column['name'], $ignored, true)) {
                $visible = !in_array($column['name'], $invisibleCols, true);
                $col = new ToManyColumn(key: $column['name'], name: $column['name'], visible: $visible);
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

            if (is_array($values) && array_key_exists($key, $values)) {
                $result[$key] = $values[$key];
                foreach ($formatters as $formatter) {
                    if (
                        !in_array($formatter::class, $ignoredFormatters, true)
                        && (
                            $formatter->showOnlyOn() === null
                            || ($isAdminPanel && $formatter->showOnlyOn() === ShowOnlyOn::ADMIN_PANEL)
                            || (!$isAdminPanel && $formatter->showOnlyOn() === ShowOnlyOn::API)
                        )
                    ) {
                        $formatter->setBuilder($this);
                        $result[$key] = $formatter->format($result[$key], $key);
                    }
                    unset($formatter); // Just performance optimization
                }
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
     * @param EntityRepository<object> $repo
     */
    public function createQueryBuilder(EntityRepository $repo, string $alias): QueryBuilder
    {
        $qb = $repo
            ->createQueryBuilder($alias)
            ->select($alias);

        $joins = array_merge(
            $this->dbSchema->getOneToOneColumns(),
            $this->dbSchema->getOneToManyColumns(),
            $this->dbSchema->getManyToOneColumns(),
            $this->dbSchema->getManyToManyColumns(),
        );

        $columnNames = array_map(fn($col) => "{$alias}.{$col->getKey()}", $this->columns);
        $joins = array_filter($joins, fn($col) => array_key_exists($col['name'], $columnNames));

        // Prevent  columns
        if (count($joins) === 0) {
            $qb->addSelect($columnNames);
        }

        foreach ($joins as $column) {
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

    /** @return  array<string, IColumn> */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getMetadata(): RecipeEntityMetadata
    {
        return $this->metadata;
    }

    protected function addIdColumnIfNotExists(): void
    {
        if (!array_key_exists('id', $this->columns)) {
            $this->columns = array_merge([
                'id' => new StringColumn(key: 'id', name: 'ID', sortable: true, visible: false),
            ], $this->columns);
        }
    }
}
