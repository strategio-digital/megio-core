<?php
declare(strict_types=1);

namespace App\Article\Recipe;

use App\Article\Database\Entity\Tag;
use Megio\Collection\CollectionRecipe;

class TagRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Tag::class;
    }
    
    public function key(): string
    {
        return 'blog-tag';
    }
}