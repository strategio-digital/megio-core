<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Resource;

use Nette\Schema\Expect;
use Nette\Utils\Strings;
use Saas\Database\Entity\Auth\Role;
use Saas\Database\EntityManager;
use Saas\Http\Request\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateRoleRequest extends Request
{
    public function __construct(protected EntityManager $em)
    {
    }
    
    public function schema(): array
    {
        return [
            'name' => Expect::string()->min(3)->max(32)->required(),
        ];
    }
    
    public function process(array $data): Response
    {
        $name = Strings::webalize($data['name']);
        
        /** @var Role|null $role */
        $role = $this->em->getAuthRoleRepo()->findOneBy(['name' => $name]);
        
        if ($role) {
            return $this->error(['This role already exists']);
        }
        
        $role = new Role();
        $role->setName($name);
        
        $this->em->persist($role);
        $this->em->flush();
        
        return $this->json([
            'id' => $role->getId(),
            'name' => $role->getName(),
            'enabled' => false
        ]);
    }
}