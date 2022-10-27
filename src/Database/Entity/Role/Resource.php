<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Database\Entity\Role;

use Framework\Database\Field\TCreatedAt;
use Framework\Database\Field\TUlid;
use Framework\Database\Repository\RoleResourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: '`fw_role_resource`')]
#[ORM\Entity(repositoryClass: RoleResourceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Resource
{
    use TUlid;
    use TCreatedAt;
    
    #[ORM\Column(unique: true, nullable: false)]
    private string $name;
    
    /** @var Collection<int, Role> */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'resources')]
    #[ORM\JoinTable(name: 'fw_role_resource_access')]
    private Collection $roles;
    
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     * @return Resource
     */
    public function setName(string $name): Resource
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }
    
    /**
     * @param Role $role
     * @return Resource
     */
    public function addRole(Role $role): Resource
    {
        if (!$this->roles->contains($role) && !$role->getResources()->contains($this)) {
            $role->getResources()->add($this);
            $this->roles->add($role);
        }
        
        return $this;
    }
}