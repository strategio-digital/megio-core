<?php
declare(strict_types=1);

namespace App\Hooray\Recipe;

use App\Hooray\Database\Entity\Shop;
use App\Hooray\Database\Enum\Api;
use Megio\Collection\CollectionRecipe;
use Megio\Collection\CollectionRequest;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\WriteBuilder\Field\EnumField;
use Megio\Collection\WriteBuilder\WriteBuilder;

class ShopRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Shop::class;
    }
    
    public function key(): string
    {
        return 'shop';
    }
    
    public function read(ReadBuilder $builder, CollectionRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema(exclude: ['orders']);
    }
    
    public function readAll(ReadBuilder $builder, CollectionRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema(exclude: ['orders', 'woocommerceConsumerKey', 'woocommerceConsumerSecret']);
    }
    
    public function create(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $builder
            ->buildByDbSchema(exclude: ['orders'], persist: true)
            ->add(new EnumField('apiClient', 'apiClient', Api::class));
    }
    
    public function update(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $builder
            ->buildByDbSchema(exclude: ['orders'], persist: true)
            ->add(new EnumField('apiClient', 'apiClient', Api::class));
    }
}