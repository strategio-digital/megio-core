<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Security\Auth;

use Saas\Database\Entity\Auth\Role;
use Saas\Database\Interface\IAuthenticable as AuthUserInterface;

class AuthUser
{
    protected ?AuthUserInterface $user = null;
    
    public function setAuthUser(AuthUserInterface $user): void
    {
        $this->user = $user;
    }
    
    public function get(): ?AuthUserInterface
    {
        return $this->user;
    }
    
    /**
     * @return string[]
     */
    public function getResources(): array
    {
        $resources = [];
        
        if ($this->user !== null) {
            foreach ($this->user->getRoles() as $role) {
                foreach ($role->getResources() as $resource) {
                    if  (!in_array($resource->getName(), $resources)) {
                        $resources[] = $resource->getName();
                    }
                }
            }
        }
        
        return $resources;
    }
    
    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        if ($this->user !== null) {
            return $this->user->getRoles()->map(fn(Role $role) => $role->getName())->toArray();
        }
        
        return [];
    }
}