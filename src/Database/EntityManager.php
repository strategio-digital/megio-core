<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Database;

use Megio\Database\Entity\Admin;
use Megio\Database\Entity\Auth\Resource;
use Megio\Database\Entity\Auth\Role;
use Megio\Database\Entity\Auth\Token;
use Megio\Database\Repository\AdminRepository;
use Megio\Database\Repository\Auth\ResourceRepository;
use Megio\Database\Repository\Auth\RoleRepository;
use Megio\Database\Repository\Auth\TokenRepository;
use Megio\Extension\Doctrine\Doctrine;

// @phpstan-ignore-next-line
class EntityManager extends \Doctrine\ORM\EntityManager
{
    /**
     * @throws \Doctrine\ORM\Exception\MissingMappingDriverImplementation
     */
    public function __construct(Doctrine $doctrine)
    {
        $em = $doctrine->getEntityManager();
        parent::__construct($em->getConnection(), $em->getConfiguration());
    }
    
    public function getAdminRepo(): AdminRepository
    {
        return $this->getRepository(Admin::class); // @phpstan-ignore-line
    }
    
    public function getAuthTokenRepo(): TokenRepository
    {
        return $this->getRepository(Token::class); // @phpstan-ignore-line
    }
    
    public function getAuthRoleRepo(): RoleRepository
    {
        return $this->getRepository(Role::class); // @phpstan-ignore-line
    }
    
    public function getAuthResourceRepo(): ResourceRepository
    {
        return $this->getRepository(Resource::class); // @phpstan-ignore-line
    }
}