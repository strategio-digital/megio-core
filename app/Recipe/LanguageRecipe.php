<?php
declare(strict_types=1);

namespace App\Recipe;

use App\Database\Entity\Localization\Language;
use Megio\Collection\CollectionRecipe;
use Megio\Collection\CollectionRequest;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\WriteBuilder\WriteBuilder;

class LanguageRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Language::class;
    }
    
    public function key(): string
    {
        return 'language';
    }
    
    public function read(ReadBuilder $builder, CollectionRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema(exclude: ['translations']);
    }
    
    public function readAll(ReadBuilder $builder, CollectionRequest $request): ReadBuilder
    {
        return $builder->buildByDbSchema(exclude: ['translations']);
    }
    
    public function create(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $builder->buildByDbSchema(exclude: ['translations']);
    }
    
    public function update(WriteBuilder $builder, CollectionRequest $request): WriteBuilder
    {
        return $builder->buildByDbSchema(exclude: ['translations']);
    }
}