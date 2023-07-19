<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Method;

use Doctrine\Common\Collections\Collection;
use Saas\Database\Entity\Auth\Role;

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