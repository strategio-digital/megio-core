<?php
declare(strict_types=1);

namespace App\Article\Recipe;

use App\Article\Database\Entity\Author;
use Megio\Collection\CollectionRecipe;

class AuthorRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Author::class;
    }
    
    public function key(): string
    {
        return 'blog-author';
    }
}