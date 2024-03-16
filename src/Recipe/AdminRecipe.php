<?php
declare(strict_types=1);

namespace Megio\Recipe;

use Megio\Collection\CollectionRecipe;
use Megio\Database\Entity\Admin;

class AdminRecipe extends CollectionRecipe
{
    public function source(): string
    {
        return Admin::class;
    }
    
    public function name(): string
    {
        return 'admin';
    }
    
    public function showOneColumns(): array
    {
        return ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    }
    
    public function showAllColumns(): array
    {
        return ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    }
}