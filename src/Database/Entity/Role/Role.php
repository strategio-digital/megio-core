<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Database\Entity\Role;

use Framework\Database\Entity\User\User;
use Framework\Database\Field\TCreatedAt;
use Framework\Database\Field\TUlid;
use Framework\Database\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: '`fw_role`')]
#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Role
{
    use TUlid;
    use TCreatedAt;
    
    #[ORM\Column(length: 32, unique: true)]
    private string $name;
    
    #[ORM\Column(name: 'is_primary', options: ['default' => 0])]
    private bool $primary = false;
    
    /** @var Collection<int, User> */
    #[ORM\OneToMany(mappedBy: 'role', targetEntity: User::class)]
    private Collection $users;
    
    /** @var Collection<int, Resource>  */
    #[ORM\ManyToMany(targetEntity: Resource::class, mappedBy: 'roles')]
    private Collection $resources;
    
    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this->primary;
    }
    
    /**
     * @param bool $primary
     * @return Role
     */
    public function setPrimary(bool $primary): Role
    {
        $this->primary = $primary;
        return $this;
    }
    
    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }
    
    /**
     * @param User $user
     * @return Role
     */
    public function addUser(User $user): Role
    {
        if (!$this->users->contains($user)) {
            $user->setRole($this);
            $this->users->add($user);
        }
        
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
     * @param Resource $resource
     * @return Role
     */
    public function addResource(Resource $resource): Role
    {
        if (!$this->resources->contains($resource) && !$resource->getRoles()->contains($this)) {
            $resource->getRoles()->add($this);
            $this->resources->add($resource);
        }
        
        return $this;
    }
}