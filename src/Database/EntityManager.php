<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Database;

use Framework\Database\Entity\Role\Resource;
use Framework\Database\Entity\Role\Role;
use Framework\Database\Entity\User\Token;
use Framework\Database\Entity\User\User;
use Framework\Database\Repository\RoleRepository;
use Framework\Database\Repository\RoleResourceRepository;
use Framework\Database\Repository\UserRepository;
use Framework\Database\Repository\UserTokenRepository;
use Framework\Extension\Doctrine\Doctrine;

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