<?php
declare(strict_types=1);

namespace Megio\Database\Entity\Auth;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Enum\ResourceType;
use Megio\Database\Field\TCreatedAt;
use Megio\Database\Field\TId;
use Megio\Database\Repository\Auth\ResourceRepository;

#[ORM\Table(name: '`auth_resource`')]
#[ORM\Entity(repositoryClass: ResourceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Resource
{
    use TId;
    use TCreatedAt;

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
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
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
     */
    public function getType(): ResourceType
    {
        return ResourceType::from($this->type);
    }

    /**
     */
    public function setType(ResourceType $type): void
    {
        $this->type = $type->value;
    }
}
