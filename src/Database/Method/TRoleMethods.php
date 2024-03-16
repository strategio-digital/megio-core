<?php
declare(strict_types=1);

namespace Megio\Database\Method;

use Doctrine\Common\Collections\Collection;
use Megio\Database\Entity\Auth\Role;

trait TRoleMethods
{
    /** @return Collection<int, Role> */
    public function getRoles(): Collection
    {
        return $this->roles;
    }
    
    /**
     * @param Role $role
     * @return self
     */
    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
        
        return $this;
    }
}