<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Megio\Collection\RecipeFinder;
use Megio\Database\Entity\Admin;
use Megio\Helper\Router;
use Megio\Http\Request\Request;
use Megio\Security\Auth\AuthUser;
use Symfony\Component\HttpFoundation\Response;

class NavbarRequest extends Request
{
    public function __construct(
        protected readonly AuthUser     $authUser,
        protected readonly RecipeFinder $recipeFinder
    )
    {
    }
    
    public function schema(): array
    {
        return [];
    }
    
    public function process(array $data): Response
    {
        $recipes = $this->recipeFinder->load()->getAll();
        $recipes = array_filter($recipes, fn($recipe) => $recipe->source() !== Admin::class);
        /** @var \Megio\Collection\ICollectionRecipe[] $recipes */
        $recipeKeys = array_map(fn($recipe) => $recipe->key(), $recipes);
        
        if (!$this->authUser->get() instanceof Admin) {
            $resources = $this->authUser->getResources();
            $recipeKeys = array_filter($recipeKeys, fn($endpoint) => in_array(Router::ROUTE_META_NAVBAR . '.' . $endpoint, $resources));
        }
        
        sort($recipeKeys);
        
        return $this->json(['items' => $recipeKeys]);
    }
}