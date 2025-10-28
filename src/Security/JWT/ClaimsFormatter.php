<?php
declare(strict_types=1);

namespace Megio\Security\JWT;

use Megio\Database\Entity\Auth\Resource;
use Megio\Database\Entity\Auth\Role;
use Megio\Database\Entity\Auth\Token;
use Megio\Database\Interface\IAuthenticable;

class ClaimsFormatter
{
    /**
     * @return array<non-empty-string, mixed>
     */
    public function format(IAuthenticable $user, Token $token): array
    {
        return [
            'bearer_token_id' => $token->getId(),
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()->map(fn(Role $role) => $role->getName())->toArray(),
                'resources' => $user->getResources()->map(fn(Resource $resource) => $resource->getName())->toArray(),
            ],
        ];
    }
}
