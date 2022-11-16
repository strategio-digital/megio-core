<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Database;

use Saas\Database\Entity\Role\Resource;
use Saas\Database\Entity\Role\Role;
use Saas\Database\Entity\User\Token;
use Saas\Database\Entity\User\User;
use Saas\Database\Repository\RoleRepository;
use Saas\Database\Repository\RoleResourceRepository;
use Saas\Database\Repository\UserRepository;
use Saas\Database\Repository\UserTokenRepository;
use Saas\Extension\Doctrine\Doctrine;

// @phpstan-ignore-next-line
class EntityManager extends \Doctrine\ORM\EntityManager
{
    public function __construct(Doctrine $doctrine)
    {
        $em = $doctrine->getEntityManager();
        parent::__construct($em->getConnection(), $em->getConfiguration());
    }
    
    public function getUserRepo(): UserRepository
    {
        return $this->getRepository(User::class); // @phpstan-ignore-line
    }
    
    public function getUserTokenRepo(): UserTokenRepository
    {
        return $this->getRepository(Token::class); // @phpstan-ignore-line
    }
    
    public function getRoleRepo(): RoleRepository
    {
        return $this->getRepository(Role::class); // @phpstan-ignore-line
    }
    
    public function getRoleResourceRepo(): RoleResourceRepository
    {
        return $this->getRepository(Resource::class); // @phpstan-ignore-line
    }
}