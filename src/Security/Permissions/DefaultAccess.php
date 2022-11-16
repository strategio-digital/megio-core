<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Security\Permissions;

class DefaultAccess
{
    /**
     * @return array<string, array<int, string>>
     */
    public static function accesses(): array
    {
        return [
            DefaultRole::Admin->name() => [],
            DefaultRole::Guest->name() => [],
            DefaultRole::Registered->name() => [
                DefaultResource::UserProfileAction->name(),
                DefaultResource::UserUploadAvatarAction->name(),
            ]
        ];
    }
}