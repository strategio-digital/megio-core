<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Resource;

use Nette\Schema\Expect;
use Saas\Database\Entity\Auth\Role;
use Saas\Database\EntityManager;
use Saas\Http\Request\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteRoleRequest extends Request
{
    public function __construct(protected EntityManager $em)
    {
    }
    
    public function schema(): array
    {
        return [
            'id' => Expect::string()->required(),
        ];
    }
    
    public function process(array $data): Response
    {
        /** @var Role|null $role */
        $role = $this->em->getAuthRoleRepo()->findOneBy(['id' => $data['id']]);
        
        if (!$role) {
            return $this->json(['message' => 'This role is already deleted'], 404);
        }
        
        $this->em->getAuthRoleRepo()->createQueryBuilder('Role')
            ->delete()
            ->where('Role.id = :id')
            ->setParameter('id', $role->getId())
            ->getQuery()
            ->execute();
        
        return $this->json(['message' => 'Role successfully deleted']);
    }
}