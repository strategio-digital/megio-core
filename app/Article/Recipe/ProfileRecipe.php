<?php
declare(strict_types=1);

namespace App\Article\Recipe;

use App\Article\Database\Entity\Profile;
use Megio\Collection\CollectionRecipe;

class ProfileRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Profile::class;
    }
    
    public function key(): string
    {
        return 'blog-profile';
    }
}