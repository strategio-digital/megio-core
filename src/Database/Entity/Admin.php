<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Entity\Auth\Role;
use Megio\Database\Entity\Auth\Resource;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TEmail;
use Megio\Database\Field\TId;
use Megio\Database\Field\TLastLogin;
use Megio\Database\Field\TPassword;
use Megio\Database\Field\TUpdatedAt;
use Megio\Database\Interface\ICrudable;
use Megio\Database\Interface\IAuthenticable;
use Megio\Database\Repository\AdminRepository;

#[ORM\Table(name: '`admin`')]
#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Admin implements ICrudable, IAuthenticable
{
    const ROLE_ADMIN = 'admin';
    
    use TId, TCreatedAt, TUpdatedAt, TEmail, TPassword, TLastLogin;
    
    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        $role = new Role();
        $role->setName(self::ROLE_ADMIN);
        return new ArrayCollection([$role]);
    }
    
    /**
     * @return Collection<int, Resource>
     */
    public function getResources(): Collection
    {
        return new ArrayCollection([]);
    }
}