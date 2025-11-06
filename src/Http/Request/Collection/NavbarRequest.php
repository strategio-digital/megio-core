<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Megio\Collection\ICollectionRecipe;
use Megio\Collection\RecipeFinder;
use Megio\Database\Entity\Admin;
use Megio\Database\Entity\Queue;
use Megio\Helper\Router;
use Megio\Http\Request\AbstractRequest;
use Megio\Security\Auth\AuthUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function array_filter;
use function array_map;
use function in_array;
use function strnatcasecmp;
use function usort;

class NavbarRequest extends AbstractRequest
{
    public function __construct(
        protected readonly AuthUser $authUser,
        protected readonly RecipeFinder $recipeFinder,
    ) {}

    public function process(Request $request): Response
    {
        $excluded = [
            Admin::class,
            Queue::class,
        ];

        $recipes = $this->recipeFinder->load()->getAll();
        $recipes = array_filter($recipes, fn(
            $recipe,
        ) => in_array($recipe->source(), $excluded, true) === false);

        /** @var ICollectionRecipe[] $recipes */
        $recipeKeys = array_map(fn(
            $recipe,
        ) => $recipe->key(), $recipes);

        if ($this->authUser->get() instanceof Admin === false) {
            $resources = $this->authUser->getResources();
            $recipeKeys = array_filter($recipeKeys, fn(
                $endpoint,
            ) => in_array(Router::ROUTE_META_NAVBAR . '.' . $endpoint, $resources, true));
        }

        $items = array_map(fn(
            $recipe,
        ) => [
            'key' => $recipe->key(),
            'name' => $recipe->name(),
        ], $recipes);

        $items = array_filter($items, fn(
            $item,
        ) => in_array($item['key'], $recipeKeys, true) === true);

        usort(
            $items,
            fn(
                $a,
                $b,
            ) => strnatcasecmp($a['name'], $b['name']),
        );

        return $this->json(['items' => $items]);
    }
}
