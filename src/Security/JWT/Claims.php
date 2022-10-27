<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Security\JWT;

use Framework\Database\Entity\Role\Resource;
use Framework\Database\Entity\User\User;

class Claims
{
    /**
     * @param User $user
     * @param array<int, Resource> $resources
     * @return array<string, mixed>
     */
    public function format(User $user, array $resources): array
    {
        $resourceNames = array_map(fn(Resource $resource) => $resource->getName(), $resources);
        
        return [
            'user_id' => $user->getId(),
            'user_email' => $user->getEmail(),
            'user_role' => $user->getRole()->getName(),
            'allowed_resources' => $resourceNames
        ];
    }
}