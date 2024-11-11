<?php
declare(strict_types=1);

namespace App\Recipe\Hooray;

use App\Database\Entity\Hooray\Localization\Translation;
use Megio\Collection\CollectionRecipe;
use Megio\Collection\CollectionRequest;
use Megio\Collection\ReadBuilder\Column\StringColumn;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\SearchBuilder\Searchable;
use Megio\Collection\SearchBuilder\SearchBuilder;

class TranslationRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Translation::class;
    }
    
    public function key(): string
    {
        return 'language-translation';
    }
    
    public function search(SearchBuilder $builder, CollectionRequest $request): SearchBuilder
    {
        return $builder
            ->keepDefaults()
            ->addSearchable(new Searchable(column: 'key', operator: 'LIKE', formatter: fn($value) => "%{$value}%"));
    }
    
    public function readAll(ReadBuilder $builder, CollectionRequest $request): ReadBuilder
    {
        return $builder
            ->buildByDbSchema(persist: true)
            ->add(new StringColumn('key', 'key', true));
    }
}