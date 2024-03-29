<?php
declare(strict_types=1);

namespace Megio\Collection\ReadBuilder;

use Doctrine\DBAL\Types\Types;
use Megio\Collection\ICollectionRecipe;
use Megio\Collection\IRecipeBuilder;
use Megio\Collection\ReadBuilder\Column\ArrayColumn;
use Megio\Collection\ReadBuilder\Column\Base\ShowOnlyOn;
use Megio\Collection\ReadBuilder\Column\BlobColumn;
use Megio\Collection\ReadBuilder\Column\DateTimeIntervalColumn;
use Megio\Collection\ReadBuilder\Column\EmailColumn;
use Megio\Collection\ReadBuilder\Column\JsonColumn;
use Megio\Collection\ReadBuilder\Column\Base\IColumn;
use Megio\Collection\ReadBuilder\Column\BooleanColumn;
use Megio\Collection\ReadBuilder\Column\DateColumn;
use Megio\Collection\ReadBuilder\Column\DateTimeColumn;
use Megio\Collection\ReadBuilder\Column\NumericColumn;
use Megio\Collection\ReadBuilder\Column\PhoneColumn;
use Megio\Collection\ReadBuilder\Column\StringColumn;
use Megio\Collection\ReadBuilder\Column\TimeColumn;
use Megio\Collection\ReadBuilder\Column\UnknownColumn;
use Megio\Collection\ReadBuilder\Column\UrlColumn;
use Megio\Collection\ReadBuilder\Column\VideoLinkColumn;
use Megio\Helper\ArrayMove;
use Nette\Utils\Strings;

class ReadBuilder implements IRecipeBuilder
{
    private ICollectionRecipe $recipe;
    
    private ReadBuilderEvent $event;
    
    /** @var array<string, IColumn> */
    private array $columns = [];
    
    /** @var array<string, class-string[]> */
    private array $ignoredFormatters = [];
    
    private bool $keepDbSchema = true;
    
    public function create(ICollectionRecipe $recipe, ReadBuilderEvent $event): self
    {
        $this->recipe = $recipe;
        $this->event = $event;
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
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function buildByDbSchema(array $exclude = [], bool $persist = false): self
    {
        $metadata = $this->recipe->getEntityMetadata();
        $dbSchema = $metadata->getFullSchemaReflectedByDoctrine();
        
        $this->addIdColumnIfNotExists();
        
        $invisibleCols = ['id', 'createdAt', 'updatedAt'];
        $ignored = array_merge($exclude, ['id']);
        
        foreach ($dbSchema as $field) {
            if (!in_array($field['name'], $ignored)) {
                $visible = !in_array($field['name'], $invisibleCols);
                $col = $this->createColumnInstance($field['type'], $field['name'], $visible);
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
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    public function format(array $values, bool $isAdminPanel): array
    {
        foreach ($this->columns as $col) {
            $key = $col->getKey();
            $formatters = $col->getFormatters();
            
            $ignoredFormatters = array_key_exists($key, $this->ignoredFormatters)
                ? $this->ignoredFormatters[$key]
                : [];
            
            foreach ($formatters as $formatter) {
                if (
                    !in_array($formatter::class, $ignoredFormatters)
                    && (
                        $formatter->showOnlyOn() === null
                        || ($isAdminPanel && $formatter->showOnlyOn() === ShowOnlyOn::ADMIN_PANEL)
                        || (!$isAdminPanel && $formatter->showOnlyOn() === ShowOnlyOn::API)
                    )
                ) {
                    $values[$key] = $formatter->format($values[$key]);
                }
                unset($formatter);
            }
        }
        
        return $values;
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
    
    public function getQbSelect(string $alias): string
    {
        return implode(', ', array_map(fn($col) => $alias . '.' . $col->getKey(), $this->columns));
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
    
    protected function createColumnInstance(string $type, string $key, bool $visible): IColumn
    {
        $keysMap = [
            'email' => new EmailColumn(key: $key, name: $key, visible: $visible),
            'phone' => new PhoneColumn(key: $key, name: $key, visible: $visible),
            'url' => new UrlColumn(key: $key, name: $key, visible: $visible),
            'video' => new VideoLinkColumn(key: $key, name: $key, visible: $visible),
        ];
        
        $columnByKey = null;
        foreach ($keysMap as $colKey => $column) {
            if (Strings::contains($key, $colKey)) {
                $columnByKey = $column;
            }
        }
        
        return match ($type) {
            Types::ASCII_STRING,
            Types::BIGINT,
            Types::BINARY,
            Types::DECIMAL,
            Types::GUID,
            Types::STRING,
            Types::TEXT => $columnByKey ?: new StringColumn(key: $key, name: $key, visible: $visible),
            
            Types::BLOB => new BlobColumn(key: $key, name: $key, visible: $visible),
            
            Types::BOOLEAN => new BooleanColumn(key: $key, name: $key, visible: $visible),
            
            Types::DATE_MUTABLE,
            Types::DATE_IMMUTABLE => new DateColumn(key: $key, name: $key, visible: $visible),
            Types::DATEINTERVAL => new DateTimeIntervalColumn(key: $key, name: $key, visible: $visible),
            
            Types::DATETIME_MUTABLE,
            Types::DATETIME_IMMUTABLE,
            Types::DATETIMETZ_MUTABLE,
            Types::DATETIMETZ_IMMUTABLE => new DateTimeColumn(key: $key, name: $key, visible: $visible),
            
            Types::FLOAT,
            Types::INTEGER,
            Types::SMALLINT => new NumericColumn(key: $key, name: $key, visible: $visible),
            
            Types::JSON => new JsonColumn(key: $key, name: $key, visible: $visible),
            Types::SIMPLE_ARRAY => new ArrayColumn(key: $key, name: $key, visible: $visible),
            
            Types::TIME_MUTABLE,
            Types::TIME_IMMUTABLE => new TimeColumn(key: $key, name: $key, visible: $visible),
            
            default => new UnknownColumn(key: $key, name: $key, visible: $visible),
        };
    }
}