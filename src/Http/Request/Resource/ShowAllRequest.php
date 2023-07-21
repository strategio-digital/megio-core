<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Resource;

use Saas\Database\Entity\Auth\Resource;
use Saas\Database\Entity\Auth\Role;
use Saas\Database\EntityManager;
use Saas\Http\Request\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowAllRequest extends Request
{
    public function __construct(protected EntityManager $em)
    {
    }
    
    public function schema(): array
    {
        return [];
    }
    
    public function process(array $data): Response
    {
        /** @var \Saas\Database\Entity\Auth\Resource[] $allResources */
        $allResources = $this->em->getAuthResourceRepo()->findAll();
        
        /** @var \Saas\Database\Entity\Auth\Role[] $roles */
        $roles = $this->em->getAuthRoleRepo()->createQueryBuilder('Role')
            ->select('Role', 'Resource')
            ->join('Role.resources', 'Resource')
            ->getQuery()
            ->getResult();
        
        $resources = [];
        
        foreach ($allResources as $resource) {
            $resources[] = [
                'id' => $resource->getId(),
                'name' => $resource->getName(),
                'type' => $resource->getType(),
                'roles' => array_map(fn(Role $role) => [
                    'id' => $role->getId(),
                    'name' => $role->getName(),
                    'enabled' => $role->getResources()->contains($resource),
                ], $roles)
            ];
        }
        
        $groups = [];
        foreach ($resources as $resource) {
            $groups[$resource['type']][] = $resource;
        }
        
        return $this->json([
            'roles' => array_map(fn(Role $role) => $role->getName(), $roles),
            'resources' => array_map(fn(Resource $resource) => [
                'id' => $resource->getId(),
                'name' => $resource->getName(),
                'type' => $resource->getType()
            ], $allResources),
            'grouped_resources_with_roles' => $groups
        ]);
    }
}