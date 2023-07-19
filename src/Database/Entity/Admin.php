<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Saas\Database\Entity\Auth\Role;
use Saas\Database\Entity\Auth\Resource;
use Saas\Database\Field\TCreatedAt;
use Saas\Database\Field\TEmail;
use Saas\Database\Field\TId;
use Saas\Database\Field\TLastLogin;
use Saas\Database\Field\TPassword;
use Saas\Database\Field\TUpdatedAt;
use Saas\Database\Interface\ICrudable;
use Saas\Database\Interface\IAuthenticable;
use Saas\Database\Repository\AdminRepository;

#[ORM\Table(name: '`admin`')]
#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Admin implements ICrudable, IAuthenticable
{
    const ROLE_ADMIN = 'admin';
    
    use TId, TCreatedAt, TUpdatedAt, TEmail, TPassword, TLastLogin;
    
    /** @var string[] */
    public array $invisibleFields = ['id', 'updatedAt'];
    
    /** @var string[] */
    public array $showAllFields = ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    
    /** @var string[] */
    public array $showOneFields = ['email', 'lastLogin', 'createdAt', 'updatedAt'];
    
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