<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Resource;

use Nette\Schema\Expect;
use Saas\Database\Entity\Auth\Resource;
use Saas\Database\Entity\Auth\Role;
use Saas\Database\EntityManager;
use Saas\Database\Enum\ResourceType;
use Saas\Database\Manager\AuthResourceManager;
use Saas\Http\Request\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowAllRequest extends Request
{
    public function __construct(protected EntityManager $em, protected AuthResourceManager $manager)
    {
    }
    
    public function schema(): array
    {
        return [
            'view_resources' => Expect::arrayOf('string')
        ];
    }
    
    public function process(array $data): Response
    {
        /** @var \Saas\Database\Entity\Auth\Resource[] $allResources */
        $allResources = $this->em->getAuthResourceRepo()->findBy([], ['type' => 'ASC', 'name' => 'ASC']);
        
        /** @var \Saas\Database\Entity\Auth\Role[] $roles */
        $roles = $this->em->getAuthRoleRepo()->createQueryBuilder('Role')
            ->select('Role', 'Resource')
            ->leftJoin('Role.resources', 'Resource')
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
        
        $diff = $this->manager->updateResources(false, $data['view_resources'], ...ResourceType::cases());
        
        return $this->json([
            'roles' => array_map(fn(Role $role) => $role->getName(), $roles),
            'resources' => array_map(fn(Resource $resource) => [
                'id' => $resource->getId(),
                'name' => $resource->getName(),
                'type' => $resource->getType()
            ], $allResources),
            'grouped_resources_with_roles' => $groups,
            'resources_diff' => [
                'to_create' => $diff['created'],
                'to_remove' => $diff['removed'],
            ]
        ]);
    }
}