<?php
declare(strict_types=1);

namespace Megio\Database\Entity\Auth;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Entity\Admin;
use Megio\Database\Entity\EntityException;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Repository\Auth\RoleRepository;

#[ORM\Table(name: '`auth_role`')]
#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Role
{
    use TId, TCreatedAt;
    
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
     * @param \Megio\Database\Entity\Auth\Resource $resource
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