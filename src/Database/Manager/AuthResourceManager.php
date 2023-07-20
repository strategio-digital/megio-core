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

class AuthResourceManager
{
    public function __construct(
        private readonly EntityManager   $em,
        private readonly RouteCollection $routes,
        private readonly CrudHelper      $crudHelper,
    )
    {
    }
    
    /**
     * Returns affected resources
     * @return array{created: string[], removed: string[]}
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function updateRouteResources(): array
    {
        /** @var \Saas\Database\Entity\Auth\Resource[] $resources */
        $resources = $this->em->getAuthResourceRepo()->findBy(['type' => ResourceType::ROUTE->value]);
        $resourceNames = array_map(fn(Resource $resource) => $resource->getName(), $resources);
        $routeNames = array_keys($this->routes->all());
        
        $create = [];
        $remove = [];
        
        foreach ($routeNames as $name) {
            if (!in_array($name, $resourceNames)) {
                $create[] = $name;
                $resource = new Resource();
                $resource->setType(ResourceType::ROUTE);
                $resource->setName($name);
                $this->em->persist($resource);
            }
        }
        
        foreach ($resourceNames as $name) {
            if (!in_array($name, $routeNames)) {
                $remove[] = $name;
            }
        }
        
        $this->em->flush();
        $this->removeResources($remove);
        
        return [
            'created' => $create,
            'removed' => $remove,
        ];
    }
    
    /**
     * Returns affected resources
     * @return array{created: string[], removed: string[]}
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function updateCollectionResources(): array
    {
        /** @var \Saas\Database\Entity\Auth\Resource[] $resources */
        $resources = $this->em->getAuthResourceRepo()->findBy(['type' => ResourceType::COLLECTION_DATA->value]);
        $resourceNames = array_map(fn(Resource $resource) => $resource->getName(), $resources);
        $collectionNames = $this->generateCollectionResourceNames();
        
        $create = [];
        $remove = [];
        
        foreach ($collectionNames as $name) {
            if (!in_array($name, $resourceNames)) {
                $create[] = $name;
                $resource = new Resource();
                $resource->setType(ResourceType::COLLECTION_DATA);
                $resource->setName($name);
                $this->em->persist($resource);
            }
        }
        
        foreach ($resourceNames as $name) {
            if (!in_array($name, $collectionNames)) {
                $remove[] = $name;
            }
        }
        
        $this->em->flush();
        $this->removeResources($remove);
        
        return [
            'created' => $create,
            'removed' => $remove,
        ];
    }
    
    /**
     * Returns affected resources
     * @return array{created: string[], removed: string[]}
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function updateCollectionNavResources(): array
    {
        
        /** @var \Saas\Database\Entity\Auth\Resource[] $resources */
        $resources = $this->em->getAuthResourceRepo()->findBy(['type' => ResourceType::COLLECTION_NAV->value]);
        $resourceNames = array_map(fn(Resource $resource) => $resource->getName(), $resources);
        $collectionNames = $this->generateCollectionNavResourceNames();
        
        $create = [];
        $remove = [];
        
        foreach ($collectionNames as $name) {
            if (!in_array($name, $resourceNames)) {
                $create[] = $name;
                $resource = new Resource();
                $resource->setType(ResourceType::COLLECTION_NAV);
                $resource->setName($name);
                $this->em->persist($resource);
            }
        }
        
        foreach ($resourceNames as $name) {
            if (!in_array($name, $collectionNames)) {
                $remove[] = $name;
            }
        }
        
        $this->em->flush();
        $this->removeResources($remove);
        
        return [
            'created' => $create,
            'removed' => $remove,
        ];
    }
    
    /**
     * @return string[]
     */
    private function generateCollectionResourceNames(): array
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
    private function generateCollectionNavResourceNames(): array
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