<?php
declare(strict_types=1);

namespace App\Recipe;

use App\Database\Entity\Order\Status;
use App\Database\Enum\OrderStatusPurpose;
use Megio\Collection\CollectionRecipe;
use Megio\Collection\CollectionRequest;
use Megio\Collection\ReadBuilder\Column\EnumColumn;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\WriteBuilder\Field\EnumField;
use Megio\Collection\WriteBuilder\Rule\NullableRule;
use Megio\Collection\WriteBuilder\WriteBuilder;

class OrderStatusRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Status::class;
    }
    
    public function key(): string
    {
        return 'order-status';
    }
    
    public function read(ReadBuilder $builder, CollectionRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema(exclude: ['orders'], persist: true);
    }
    
    public function readAll(ReadBuilder $builder, CollectionRequest $request): ReadBuilder
    {
        return $builder
            ->buildByDbSchema(exclude: ['orders'], persist: true)
            ->add(new EnumColumn('purpose', 'purpose'));
    }
    
    public function create(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $builder
            ->buildByDbSchema(exclude: ['orders'], persist: true)
            ->add(new EnumField('purpose', 'purpose', OrderStatusPurpose::class, rules: [new NullableRule()]));
    }
    
    public function update(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $builder
            ->buildByDbSchema(exclude: ['orders'], persist: true)
            ->add(new EnumField('purpose', 'purpose', OrderStatusPurpose::class, rules: [new NullableRule()]));
    }
}