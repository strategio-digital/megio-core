<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Security\JWT;

use Saas\Database\Entity\Auth\Resource;
use Saas\Database\Entity\Auth\Role;
use Saas\Database\Entity\Auth\Token;
use Saas\Database\Interface\IAuthenticable;

class ClaimsFormatter
{
    /**
     * @param \Saas\Database\Interface\IAuthenticable $user
     * @param \Saas\Database\Entity\Auth\Token $token
     * @return array<string, mixed>
     */
    public function format(IAuthenticable $user, Token $token): array
    {
        return [
            'bearer_token_id' => $token->getId(),
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()->map(fn(Role $role) => $role->getName())->toArray(),
                'resources' => $user->getResources()->map(fn(Resource $resource) => $resource->getName())->toArray()
            ]
        ];
    }
}