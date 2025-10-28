<?php
declare(strict_types=1);

namespace Megio\Security\Auth;

use Megio\Database\Entity\Auth\Role;
use Megio\Database\Interface\IAuthenticable as AuthUserInterface;

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
                    if (!in_array($resource->getName(), $resources, true)) {
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
