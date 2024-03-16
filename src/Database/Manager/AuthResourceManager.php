<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Database\Manager;

use Megio\Collection\RecipeFinder;
use Nette\Utils\Strings;
use Megio\Database\Entity\Admin;
use Megio\Database\Entity\Auth\Resource;
use Megio\Database\EntityManager;
use Megio\Database\Enum\ResourceType;
use Megio\Helper\Router;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

readonly class AuthResourceManager
{
    public function __construct(
        private EntityManager   $em,
        private RouteCollection $routes,
        private RecipeFinder    $recipeFinder,
    )
    {
    }
    
    /**
     * @param bool $flush
     * @param string[] $viewResources
     * @param \Megio\Database\Enum\ResourceType ...$types
     * @return array{created: string[], removed: string[]}
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function updateResources(bool $flush = true, array $viewResources = [], ResourceType...$types): array
    {
        $created = [];
        $removed = [];
        
        foreach ($types as $type) {
            $methodName = $type->getResourcesMethodName();
            
            if ($type === ResourceType::ROUTER_VIEW) {
                $sourceNames = $this->$methodName($viewResources);
            } else {
                $sourceNames = $this->$methodName();
            }
            
            $diff = $this->diffNames($sourceNames, $type);
            $created = array_merge($created, $diff['created']);
            $removed = array_merge($removed, $diff['removed']);
            
            if ($flush) {
                foreach ($diff['created'] as $name) {
                    $resource = new Resource();
                    $resource->setType($type);
                    $resource->setName($name);
                    $this->em->persist($resource);
                }
            }
            
            if ($flush) {
                $this->removeResources($diff['removed']);
                $this->em->flush();
            }
        }
        
        return [
            'created' => $created,
            'removed' => $removed,
        ];
    }
    
    /**
     * @param string[] $sourceNames
     * @return array{created: string[], removed: string[]}
     */
    public function diffNames(array $sourceNames, ResourceType $type): array
    {
        $resources = $this->em->getAuthResourceRepo()->findBy(['type' => $type->value]);
        $resourceNames = array_map(fn(Resource $resource) => $resource->getName(), $resources);
        
        $create = [];
        $remove = [];
        
        foreach ($sourceNames as $name) {
            if (!in_array($name, $resourceNames)) {
                $create[] = $name;
            }
        }
        
        foreach ($resourceNames as $name) {
            if (!in_array($name, $sourceNames)) {
                $remove[] = $name;
            }
        }
        
        return [
            'created' => $create,
            'removed' => $remove,
        ];
    }
    
    /**
     * @return string[]
     */
    public function routerResources(): array
    {
        $routes = $this->routes->all();
        $routes = array_filter($routes, fn(Route $route) => $route->getOption('auth') !== false && $route->getOption('inResources') !== false);
        return array_keys($routes);
    }
    
    /**
     * @param string[] $viewResources
     * @return string[]
     */
    public function routerViewResources(array $viewResources): array
    {
        return $viewResources;
    }
    
    /**
     * @return string[]
     */
    public function collectionDataResources(): array
    {
        $excluded = [Admin::class];
        $recipes = $this->recipeFinder->load()->getAll();
        
        /** @var \Megio\Collection\ICollectionRecipe[] $recipes */
        $recipes = array_filter($recipes, fn($recipe) => !in_array($recipe->source(), $excluded));
        $recipeNames = array_map(fn($recipe) => $recipe->name(), $recipes);
        
        $resourceNames = array_keys($this->routes->all());
        $collectionRouteNames = array_filter($resourceNames, fn($name) => Strings::startsWith($name, Router::ROUTE_COLLECTION_PREFIX));
        
        $names = [];
        foreach ($recipeNames as $recipeName) {
            foreach ($collectionRouteNames as $routeName) {
                $names[] = $routeName . '.' . $recipeName;
            }
        }
        
        return $names;
    }
    
    /**
     * @return string[]
     */
    public function collectionNavResources(): array
    {
        $excluded = [Admin::class];
        $recipes = $this->recipeFinder->load()->getAll();
        
        /** @var \Megio\Collection\ICollectionRecipe[] $recipes */
        $recipes = array_filter($recipes, fn($recipe) => !in_array($recipe->source(), $excluded));
        $recipeNames = array_map(fn($recipe) => $recipe->name(), $recipes);
        
        $names = [];
        foreach ($recipeNames as $recipeName) {
            $names[] = Router::ROUTE_META_NAVBAR . '.' . $recipeName;
        }
        
        return $names;
    }
    
    /**
     * @param string[] $names
     * @return void
     */
    private function removeResources(array $names): void
    {
        $this->em->getAuthResourceRepo()
            ->createQueryBuilder('resource')
            ->delete()
            ->andWhere('resource.name IN (:names)')
            ->setParameter('names', $names)
            ->getQuery()->execute();
    }
}