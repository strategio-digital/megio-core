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
            DefaultRole::Admin->name() => [
                DefaultResource::AuthRevokeToken->name(),
                DefaultResource::UserShow->name(),
                DefaultResource::UserShowOne->name(),
                DefaultResource::UserCreate->name(),
                DefaultResource::UserDelete->name(),
                DefaultResource::SaasCrudShow->name(),
                DefaultResource::SaasCrudShowOne->name(),
                DefaultResource::SaasCrudCrete->name(),
                DefaultResource::SaasCrudUpdate->name(),
                DefaultResource::SaasCrudDelete->name(),
                DefaultResource::SaasCollectionMetaNavbar->name(),
            ],
            DefaultRole::Editor->name() => [
                DefaultResource::UserProfileAction->name(),
                DefaultResource::UserUploadAvatarAction->name(),
            ],
            DefaultRole::User->name() => [
                DefaultResource::UserProfileAction->name(),
                DefaultResource::UserUploadAvatarAction->name(),
            ]
        ];
    }
}