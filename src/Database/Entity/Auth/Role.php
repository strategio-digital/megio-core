<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Database\Entity\Auth;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Saas\Database\Entity\Admin;
use Saas\Database\Entity\EntityException;
use Saas\Database\Field\TCreatedAt;
use Saas\Database\Field\TId;
use Saas\Database\Repository\Auth\RoleRepository;

#[ORM\Table(name: '`auth_role`')]
#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Role
{
    use TId;
    use TCreatedAt;
    
    #[ORM\Column(length: 32, unique: true)]
    private string $name;
    
    /** @var Collection<int, Resource>  */
    #[ORM\ManyToMany(targetEntity: Resource::class, inversedBy: 'roles')]
    #[ORM\JoinTable(name: '`auth_role_has_resource`')]
    private Collection $resources;
    
    public function __construct()
    {
        $this->resources = new ArrayCollection();
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
     * @return Role
     */
    public function setName(string $name): Role
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return Collection<int, Resource>
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }
    
    /**
     * @param \Saas\Database\Entity\Auth\Resource $resource
     * @return $this
     */
    public function addResource(Resource $resource): Role
    {
        if (!$this->resources->contains($resource) && !$resource->getRoles()->contains($this)) {
            $resource->getRoles()->add($this);
            $this->resources->add($resource);
        }
        
        return $this;
    }
    
    #[ORM\PreFlush]
    #[ORM\PrePersist]
    public function preventAdminRole(): void
    {
        if ($this->name === Admin::ROLE_ADMIN) {
            throw new EntityException('You can not create admin role, admin role is default.');
        }
    }
}