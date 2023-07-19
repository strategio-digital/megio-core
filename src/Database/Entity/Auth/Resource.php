<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Database\Entity\Auth;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Saas\Database\Enum\ResourceType;
use Saas\Database\Field\TCreatedAt;
use Saas\Database\Field\TId;
use Saas\Database\Repository\Auth\ResourceRepository;

#[ORM\Table(name: '`auth_resource`')]
#[ORM\Entity(repositoryClass: ResourceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Resource
{
    use TId, TCreatedAt;
    
    #[ORM\Column(unique: true, nullable: false)]
    private string $name;
    
    #[ORM\Column(length: 32, nullable: false)]
    private string $type;
    
    /** @var Collection<int, Role> */
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'resources')]
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
    
    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
    
    /**
     * @param ResourceType $type
     */
    public function setType(ResourceType $type): void
    {
        $this->type = $type->value;
    }
}