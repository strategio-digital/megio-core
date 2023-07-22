<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Manager;

use Nette\Utils\Strings;
use Saas\Database\CrudHelper\CrudHelper;
use Saas\Database\Entity\Auth\Resource;
use Saas\Database\EntityManager;
use Saas\Database\Enum\ResourceType;
use Saas\Helper\Router;
use Symfony\Component\Routing\RouteCollection;

readonly class AuthResourceManager
{
    public function __construct(
        private EntityManager   $em,
        private RouteCollection $routes,
        private CrudHelper      $crudHelper,
    )
    {
    }
    
    /**
     * @param bool $flush
     * @param string[] $viewResources
     * @param \Saas\Database\Enum\ResourceType ...$types
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
        /** @var \Saas\Database\Entity\Auth\Resource[] $resources */
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
        return array_keys($this->routes->all());
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
        $resourceNames = array_keys($this->routes->all());
        $tables = array_map(fn($entity) => $entity['table'], $this->crudHelper->getAllEntities());
        $collectionRouteNames = array_filter($resourceNames, fn($name) => Strings::startsWith($name, Router::ROUTE_COLLECTION_PREFIX));
        
        $names = [];
        
        foreach ($tables as $tableName) {
            foreach ($collectionRouteNames as $routeName) {
                $names[] = $routeName . '.' . $tableName;
            }
        }
        
        return $names;
    }
    
    /**
     * @return string[]
     */
    public function collectionNavResources(): array
    {
        $tables = array_map(fn($entity) => $entity['table'], $this->crudHelper->getAllEntities());
        $names = [];
        
        foreach ($tables as $tableName) {
            $names[] = Router::ROUTE_META_NAVBAR . '.' . $tableName;
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
            ->createQueryBuilder('Resource')
            ->delete()
            ->andWhere('Resource.name IN (:names)')
            ->setParameter('names', $names)
            ->getQuery()->execute();
    }
}