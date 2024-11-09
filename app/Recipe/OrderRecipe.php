<?php
declare(strict_types=1);

namespace App\Recipe;

use App\Database\Entity\Order\Order;
use Megio\Collection\CollectionRecipe;
use Megio\Collection\CollectionRequest;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\SearchBuilder\Searchable;
use Megio\Collection\SearchBuilder\SearchBuilder;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Database\Limits;

class OrderRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Order::class;
    }
    
    public function key(): string
    {
        return 'order';
    }
    
    public function search(SearchBuilder $builder, CollectionRequest $request): SearchBuilder
    {
        return $builder
            ->keepDefaults()
            ->addSearchable(new Searchable(column: 'orderNumber', operator: 'LIKE', formatter: fn($value) => "%{$value}%"))
            ->addSearchable(new Searchable(column: 'woocommerceId', enabled: fn($value) => is_numeric($value) && $value <= Limits::POSTGRES_MAX_INT))
            ->addSearchable(new Searchable(column: 'email', operator: 'LIKE', formatter: fn($value) => "%{$value}%"))
            ->addSearchable(new Searchable(column: 'key', relation: 'status'))
            ->addSearchable(new Searchable(column: 'cuteName', relation: 'status'));
    }
    
    public function readAll(ReadBuilder $builder, CollectionRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema(exclude: ['comments']);
    }
    
    public function create(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $builder->buildByDbSchema(exclude: ['comments']);
    }
    
    public function update(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $builder->buildByDbSchema(exclude: ['comments']);
    }
}