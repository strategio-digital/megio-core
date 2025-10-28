<?php
declare(strict_types=1);

namespace Megio\Collection\SearchBuilder;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Megio\Collection\CollectionRequest;
use Symfony\Component\Uid\UuidV6;

class SearchBuilder
{
    protected QueryBuilder $queryBuilder;

    protected CollectionRequest $request;

    /** @var array<string, Searchable> */
    protected array $searchables = [];

    /** @var string[] */
    protected array $extraSearchables = [];

    public function create(
        QueryBuilder $qb,
        CollectionRequest $request,
    ): self {
        $this->queryBuilder = $qb;
        $this->request = $request;

        return $this;
    }

    public function build(): QueryBuilder
    {
        // Text search
        $searchText = $this->request->getSearchText();

        if ($searchText !== null) {
            $whereDql = [];

            $validSearchables = array_filter($this->searchables, fn(
                Searchable $searchable,
            ) => $searchable->isEnabled($searchText));

            foreach ($validSearchables as $searchable) {
                $relationCol = $searchable->getRelation();
                $colName = 'entity.' . $searchable->getColumn();

                if ($relationCol !== null) {
                    $alias = 'alias_' . $searchable->getRelation() . '_' . $searchable->getColumn();
                    $this->queryBuilder->leftJoin("entity.{$relationCol}", $alias);
                    $colName = "{$alias}.{$searchable->getColumn()}";
                }

                $paramName = 'param_' . str_replace('.', '_', $colName);
                $value = $searchable->hasFormatter() ? $searchable->format($searchText) : $searchText;

                $whereDql[] = [
                    'dql' => "$colName {$searchable->getOperator()} :{$paramName}",
                    'paramName' => $paramName,
                    'paramValue' => $value,
                ];
            }

            if (count($whereDql) !== 0) {
                $where = implode(' OR ', array_map(fn(
                    $where,
                ) => $where['dql'], $whereDql));
                $this->queryBuilder->orWhere($where);

                foreach ($whereDql as $where) {
                    $this->queryBuilder->setParameter($where['paramName'], $where['paramValue']);
                }
            }
        }

        return $this->queryBuilder;
    }

    public function keepDefaults(): self
    {
        $this->addSearchable(
            new Searchable(
                column: 'id',
                operator: '=',
                enabled: fn(
                    $value,
                ) => UuidV6::isValid($value),
            ),
        );

        $this->addSearchable(
            new Searchable(
                column: 'createdAt',
                operator: '=',
                enabled: fn(
                    $value,
                ) => DateTime::createFromFormat('Y-m-d H:i:s', $value) !== false,
            ),
        );

        $this->addSearchable(
            new Searchable(
                column: 'updatedAt',
                operator: '=',
                enabled: fn(
                    $value,
                ) => DateTime::createFromFormat('Y-m-d H:i:s', $value) !== false,
            ),
        );

        return $this;
    }

    public function addSearchable(Searchable $searchable): self
    {
        $this->searchables[$searchable->getColumn()] = $searchable;
        return $this;
    }

    /**
     * @param string[] $columnNames
     */
    public function addSearchablesToSchema(array $columnNames): self
    {
        // Add only unique values
        $this->extraSearchables = $columnNames;
        return $this;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function getRequest(): CollectionRequest
    {
        return $this->request;
    }

    /** @return array{
     *    searchables: array{column: string, relation: string|null}[]
     * }
     */
    public function toArray(): array
    {
        $searchables = array_map(fn(
            Searchable $searchable,
        ) => $searchable->toArray(), $this->searchables);

        foreach ($this->extraSearchables as $columnName) {
            $searchables[] = [
                'column' => $columnName,
                'relation' => null,
            ];
        }

        return [
            'searchables' => array_values($searchables),
        ];
    }
}
