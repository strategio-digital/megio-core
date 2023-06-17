<?php

namespace Saas\Security\Permissions;

enum DefaultResource implements IResource
{
    case AuthRevokeToken;
    
    case UserShow;
    case UserShowOne;
    case UserCreate;
    case UserDelete;
    case UserProfileAction;
    case UserUploadAvatarAction;
    case SaasCrudShow;
    case SaasCrudShowOne;
    case SaasCrudCrete;
    case SaasCrudUpdate;
    case SaasCrudDelete;
    case SaasCollectionMetaNavbar;
    
    public function name(): string
    {
        return match ($this) {
            self::AuthRevokeToken => 'auth.revoke-token',
            self::UserShow => 'user.show',
            self::UserShowOne => 'user.show-one',
            self::UserCreate => 'user.create',
            self::UserDelete => 'user.delete',
            self::UserProfileAction => 'user.profile',
            self::UserUploadAvatarAction => 'user.upload-avatar',
            self::SaasCrudShow => 'saas.crud.show',
            self::SaasCrudShowOne => 'saas.crud.show-one',
            self::SaasCrudCrete => 'saas.crud.create',
            self::SaasCrudUpdate => 'saas.crud.update',
            self::SaasCrudDelete => 'saas.crud.delete',
            self::SaasCollectionMetaNavbar => 'saas.collection.meta.navbar',
        };
    }
}
