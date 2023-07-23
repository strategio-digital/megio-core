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
            'view_resources' => Expect::arrayOf('string')->required(),
            'make_view_diff' => Expect::bool()->default(false)->required(),
        ];
    }
    
    public function process(array $data): Response
    {
        /** @var \Saas\Database\Entity\Auth\Resource[] $resources */
        $resources = $this->em->getAuthResourceRepo()->findBy([], ['type' => 'ASC', 'name' => 'ASC']);
        
        /** @var \Saas\Database\Entity\Auth\Role[] $roles */
        $roles = $this->em->getAuthRoleRepo()->createQueryBuilder('Role')
            ->select('Role', 'Resource')
            ->leftJoin('Role.resources', 'Resource')
            ->getQuery()
            ->getResult();
        
        $groups = $this->groupResources($resources, $roles);
        $types = array_filter(ResourceType::cases(), fn($case) => $case !== ResourceType::ROUTER_VIEW);
        
        if ($data['make_view_diff']) {
            $types = ResourceType::cases();
        }
        
        $diff = $this->manager->updateResources(false, $data['view_resources'], ...$types);
        
        return $this->json([
            'roles' => array_map(fn(Role $role) => $role->getName(), $roles),
            'resources' => array_map(fn(Resource $resource) => [
                'id' => $resource->getId(),
                'name' => $resource->getName(),
                'type' => $resource->getType()
            ], $resources),
            'grouped_resources_with_roles' => $groups,
            'resources_diff' => [
                'to_create' => $diff['created'],
                'to_remove' => $diff['removed'],
            ]
        ]);
    }
    
    /**
     * @param Resource[] $resources
     * @param Role[] $roles
     * @return array<string, mixed>
     */
    private function groupResources(array $resources, array $roles): array
    {
        $result = [];
        
        foreach ($resources as $resource) {
            $result[] = [
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
        foreach ($result as $resource) {
            $groups[$resource['type']][] = $resource;
        }
        
        return $groups;
    }
}