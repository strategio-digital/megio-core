<?php
declare(strict_types=1);

namespace App\Hooray\Recipe;

use App\Hooray\Database\Entity\Order\Comment;
use Megio\Collection\CollectionRecipe;
use Megio\Collection\CollectionRequest;
use Megio\Collection\WriteBuilder\WriteBuilder;

class OrderCommentRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Comment::class;
    }
    
    public function key(): string
    {
        return 'order-comment';
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