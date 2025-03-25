<?php
declare(strict_types=1);

namespace App\Hooray\Recipe;

use App\Hooray\Database\Entity\Customer\ApprovalPage;
use Megio\Collection\CollectionRecipe;
use Megio\Collection\CollectionRequest;
use Megio\Collection\Formatter\CallableFormatter;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\SearchBuilder\Searchable;
use Megio\Collection\SearchBuilder\SearchBuilder;
use Megio\Collection\WriteBuilder\Field\TextAreaField;
use Megio\Collection\WriteBuilder\Rule\JsonStringRule;
use Megio\Collection\WriteBuilder\Serializer\CallableSerializer;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Database\Limits;

class ApprovalPageRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return ApprovalPage::class;
    }
    
    public function key(): string
    {
        return 'approval-page';
    }
    
    public function search(SearchBuilder $builder, CollectionRequest $request): SearchBuilder
    {
        return $builder
            ->keepDefaults()
            ->addSearchable(new Searchable(
                    column: 'orderNumber',
                    relation: 'order_',
                    operator: 'LIKE',
                    formatter: fn($value) => "%{$value}%")
            )
            ->addSearchable(new Searchable(
                    column: 'woocommerceId',
                    relation: 'order_',
                    enabled: fn($value) => is_numeric($value) && $value <= Limits::POSTGRES_MAX_INT)
            )
            ->addSearchable(new Searchable(
                    column: 'email',
                    relation: 'order_',
                    operator: 'LIKE',
                    formatter: fn($value) => "%{$value}%")
            );
    }
    
    public function readAll(ReadBuilder $builder, CollectionRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema(exclude: ['items']);
    }
    
    public function create(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $this->commonBuilder($builder);
    }
    
    public function update(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $this->commonBuilder($builder);
    }
    
    private function commonBuilder(WriteBuilder $builder): WriteBuilder
    {
        return $builder
            ->buildByDbSchema(persist: true)
            ->add(new TextAreaField(
                name: 'items',
                label: 'items',
                rules: [
                    new JsonStringRule()
                ],
                serializers: [
                    new CallableSerializer(fn($payload) => json_decode($payload, true))
                ],
                formatters: [
                    new CallableFormatter(fn($payload) => json_encode($payload))
                ],
                attrs: [
                    'fullWidth' => true,
                ]
            ));
    }
}