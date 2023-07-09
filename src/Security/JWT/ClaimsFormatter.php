<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Security\JWT;

use Saas\Database\Entity\Auth\Resource;
use Saas\Database\Entity\Auth\Role;
use Saas\Database\Interface\AuthUser;

class ClaimsFormatter
{
    /**
     * @param \Saas\Database\Interface\AuthUser $user
     * @param array<int, \Saas\Database\Entity\Auth\Resource>|null $resources
     * @param array<int, \Saas\Database\Entity\Auth\Role> $roles
     * @return array<string, mixed>
     */
    public function format(AuthUser $user, array $roles, ?array $resources): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => array_map(fn(Role $role) => $role->getName(), $roles),
            'resources' => $resources ? array_map(fn(Resource $resource) => $resource->getName(), $resources) : null
        ];
    }
}