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
    case CrudShow;
    case CrudShowOne;
    case CrudCrete;
    case CrudUpdate;
    case CrudDelete;
    
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
            self::CrudShow => 'crud.show',
            self::CrudShowOne => 'crud.show-one',
            self::CrudCrete => 'crud.create',
            self::CrudUpdate => 'crud.update',
            self::CrudDelete => 'crud.delete',
        };
    }
}
