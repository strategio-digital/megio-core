<?php
declare(strict_types=1);

namespace Megio\Collection\SearchBuilder;

use Doctrine\ORM\QueryBuilder;
use Megio\Collection\CollectionRequest;

class SearchBuilder
{
    protected QueryBuilder $queryBuilder;
    protected CollectionRequest $request;
    
    /** @var array<string, Searchable> */
    protected array $searchables = [];
    
    public function create(QueryBuilder $qb, CollectionRequest $request): self
    {
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
            
            foreach ($this->searchables as $searchable) {
                $relationCol = $searchable->getRelation();
                $colName = 'entity.' . $searchable->getColumn();
                
                if ($relationCol !== null) {
                    $alias = 'alias_' . $searchable->getRelation() . '_' . $searchable->getColumn();
                    $this->queryBuilder->leftJoin("entity.{$relationCol}", $alias);
                    $colName = "{$alias}.{$searchable->getColumn()}";
                }
                
                $paramName = 'param_' . str_replace('.', '_', $colName);
                $value = $searchable->hasFormatter() ? $searchable->format($searchText) : "%{$searchText}%";
                
                $whereDql[] = [
                    'dql' => "{$colName} {$searchable->getOperator()} :{$paramName}",
                    'paramName' => $paramName,
                    'paramValue' => $value
                ];
            }
            
            if (count($whereDql) !== 0) {
                $where = implode(' OR ', array_map(fn($where) => $where['dql'], $whereDql));
                $this->queryBuilder->andWhere($where);
                
                foreach ($whereDql as $where) {
                    $this->queryBuilder->setParameter($where['paramName'], $where['paramValue']);
                }
            }
        }
        
        return $this->queryBuilder;
    }
    
    /**
     * @param string[] $searchables
     */
    public function keepDefaults(array $searchables = ['id', 'createdAt', 'updatedAt']): self
    {
        foreach ($searchables as $columnName) {
            $this->addSearchable(new Searchable($columnName));
        }
        
        return $this;
    }
    
    public function addSearchable(Searchable $searchable): self
    {
        $this->searchables[$searchable->getColumn()] = $searchable;
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
     *    searchables: array{column: string, relation: string|null, operator: string}[]
     * }
     */
    public function toArray(): array
    {
        $searchables = array_map(fn(Searchable $searchable) => $searchable->toArray(), $this->searchables);
        
        return [
            'searchables' => array_values($searchables),
        ];
    }
}