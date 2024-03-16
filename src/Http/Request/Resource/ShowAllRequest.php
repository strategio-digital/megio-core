<?php
declare(strict_types=1);

namespace Megio\Http\Request\Resource;

use Nette\Schema\Expect;
use Megio\Database\Entity\Auth\Resource;
use Megio\Database\Entity\Auth\Role;
use Megio\Database\EntityManager;
use Megio\Database\Enum\ResourceType;
use Megio\Database\Manager\AuthResourceManager;
use Megio\Http\Request\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;

class ShowAllRequest extends Request
{
    public function __construct(
        protected EntityManager       $em,
        protected AuthResourceManager $manager,
        protected RouteCollection     $routes
    )
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
        $resources = $this->em->getAuthResourceRepo()->findBy([], ['type' => 'ASC', 'name' => 'ASC']);
        
        /** @var \Megio\Database\Entity\Auth\Role[] $roles */
        $roles = $this->em->getAuthRoleRepo()->createQueryBuilder('role')
            ->select('role', 'resource')
            ->leftJoin('role.resources', 'resource')
            ->orderBy('role.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
        
        $groups = $this->groupResources($resources, $roles);
        $groups = $this->sortCollectionDataResources($groups);
        
        $types = array_filter(ResourceType::cases(), fn($case) => $case !== ResourceType::ROUTER_VIEW);
        
        if ($data['make_view_diff']) {
            $types = ResourceType::cases();
        }
        
        $diff = $this->manager->updateResources(false, $data['view_resources'], ...$types);
        
        $groupedResourcesWithRoles = [];
        foreach ($groups as $name => $group) {
            $groupedResourcesWithRoles[] = [
                'groupName' => $name,
                'resources' => $group
            ];
        }
        
        return $this->json([
            'roles' => array_map(fn(Role $role) => [ 'id' => $role->getId(), 'name' => $role->getName() ], $roles),
            'resources' => array_map(fn(Resource $resource) => [
                'id' => $resource->getId(),
                'name' => $resource->getName(),
                'type' => $resource->getType()->value
            ], $resources),
            'grouped_resources_with_roles' => $groupedResourcesWithRoles,
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
                'type' => $resource->getType()->value,
                'hint' => $this->createHint($resource->getType(),  $resource->getName()),
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
    
    /**
     * @param array<string, mixed> $resources
     * @return array<string, mixed>
     */
    private function sortCollectionDataResources(array $resources): array
    {
        $key = ResourceType::COLLECTION_RECIPE->value;
        if (array_key_exists($key, $resources)) {
            $groups = [];
            foreach ($resources[$key] as $value) {
                $groupName = pathinfo($value['name'], PATHINFO_EXTENSION);
                $groups[$groupName][] = $value;
            }
            
            $resources[$key] = [];
            foreach ($groups as $value) {
                foreach ($value as $item) {
                    $resources[$key][] = $item;
                }
            }
        }
        
        return $resources;
    }
    
    private function createHint(ResourceType $type, string $name): string|null
    {
        if ($type === ResourceType::ROUTER_VIEW) {
            return null;
        }
        
        if ($type === ResourceType::COLLECTION_NAV || $type === ResourceType::COLLECTION_RECIPE) {
            $last = pathinfo($name, PATHINFO_EXTENSION);
            $name = substr_replace($name, '', -strlen($last) - 1, strlen($last) + 1);
        }
        
        $route = $this->routes->get($name);
        
        if (!$route) {
            return null;
        }
        
        $methods = implode(', ', $route->getMethods());
        
        return "[$methods] {$route->getPath()}";
    }
}