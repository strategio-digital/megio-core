<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
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
        $recipeNames = array_map(fn($recipe) => $recipe->name(), $recipes);
        
        if (!$this->authUser->get() instanceof Admin) {
            $resources = $this->authUser->getResources();
            $recipeNames = array_filter($recipeNames, fn($endpoint) => in_array(Router::ROUTE_META_NAVBAR . '.' . $endpoint, $resources));
        }
        
        sort($recipeNames);
        
        return $this->json(['items' => $recipeNames]);
    }
}