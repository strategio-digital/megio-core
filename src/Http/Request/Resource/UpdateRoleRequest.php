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
use Saas\Http\Request\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateRoleRequest extends Request
{
    public function __construct(protected EntityManager $em)
    {
    }
    
    public function schema(): array
    {
        return [
            'resource_id' => Expect::string()->required(),
            'role_id' => Expect::string()->required(),
            'enable' => Expect::bool()->required(),
        ];
    }
    
    public function process(array $data): Response
    {
        /** @var Resource|null $resource */
        $resource = $this->em->getAuthResourceRepo()->findOneBy(['id' => $data['resource_id']]);
        
        /** @var Role|null $role */
        $role = $this->em->getAuthRoleRepo()->findOneBy(['id' => $data['role_id']]);
        
        if (!$role || !$resource) {
            return $this->error(['Role or resource not found'], 404);
        }
        
        if ($data['enable'] === true) {
            $role->addResource($resource);
        } else {
            $role->getResources()->removeElement($resource);
        }
        
        $this->em->flush($role);
        
        return $this->json(['message' => 'Resources successfully updated']);
    }
}