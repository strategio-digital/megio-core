<?php
declare(strict_types=1);

namespace Megio\Collection\SearchBuilder;

class Searchable
{
    /** @var array<int, callable> */
    private array $formatter = [];

    /** @var array<int, callable> */
    private array $enabled = [];

    public function __construct(
        protected string  $column,
        protected ?string $relation = null,
        protected string  $operator = '=',
        ?callable         $formatter = null,
        ?callable         $enabled = null,
    ) {
        if ($formatter !== null) {
            $this->formatter[] = $formatter;
        }

        if ($enabled !== null) {
            $this->enabled[] = $enabled;
        }
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function format(?string $value): mixed
    {
        return $this->formatter[0]($value);
    }

    public function isEnabled(?string $value): bool
    {
        if (count($this->enabled) === 0) {
            return true;
        }

        return $this->enabled[0]($value);
    }

    public function hasFormatter(): bool
    {
        return count($this->formatter) !== 0;
    }

    /**
     * @return array{column: string, relation: string|null}
     */
    public function toArray(): array
    {
        return [
            'column' => $this->column,
            'relation' => $this->relation,
        ];
    }
}
