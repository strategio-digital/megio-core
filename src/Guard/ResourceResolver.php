<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Guard;

use Framework\Database\Entity\Role\Resource;
use Framework\Database\EntityManager;
use Framework\Http\Request\Request;
use Framework\Http\Response\Response;
use Framework\Security\JWT\Jwt;
use Framework\Security\Permissions\DefaultRole;
use Framework\Security\Permissions\IResource;

class ResourceResolver
{
    public function __construct(
        protected readonly Request       $request,
        protected readonly Response      $response,
        protected readonly Auth          $auth,
        protected readonly EntityManager $em,
        protected readonly Jwt           $jwt
    )
    {
    }
    
    /**
     * @param array<int, IResource> $resources
     * @return void
     * @throws \Exception
     */
    public function beforeAction(array $resources): void
    {
        $roleName = $this->auth->getRoleName();
        
        $repo = $this->em->getRoleResourceRepo();
        
        $qb = $repo->createQueryBuilder('Resource')
            ->addSelect('Resource')
            ->innerJoin('Resource.roles', 'Role')
            ->andWhere('Resource.name IN (:resource_names)')
            ->setParameter('resource_names', array_map(fn($resource) => $resource->name(), $resources));
        
        if ($roleName !== DefaultRole::Admin->name()) {
            $qb->andWhere('Role.name = :role_name')
                ->setParameter('role_name', $roleName);
        }
        
        $rowResourceNames = array_map(fn(Resource $row) => $row->getName(), $qb->getQuery()->getResult());
        
        foreach ($resources as $resource) {
            if (!in_array($resource->name(), $rowResourceNames)) {
                $this->response->sendError(['message' => "Your role '{$roleName}' does not have permissions for '{$resource->name()}' resource."], 401);
            }
        }
    }
}