<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Database;

use Saas\Database\Entity\Admin;
use Saas\Database\Entity\Auth\Resource;
use Saas\Database\Entity\Auth\Role;
use Saas\Database\Entity\Auth\Token;
use Saas\Database\Repository\AdminRepository;
use Saas\Database\Repository\Auth\ResourceRepository;
use Saas\Database\Repository\Auth\RoleRepository;
use Saas\Database\Repository\Auth\TokenRepository;
use Saas\Extension\Doctrine\Doctrine;

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