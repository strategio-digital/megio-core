<?php

namespace Saas\Security\Permissions;

enum DefaultResource implements IResource
{
    case UserShow;
    case UserShowOne;
    case UserCreate;
    case UserDelete;
    
    case UserRevoke;
    case UserProfileAction;
    case UserUploadAvatarAction;
    
    public function name(): string
    {
        return match ($this) {
            self::UserShow => 'user-show',
            self::UserShowOne => 'user-show-one',
            self::UserCreate => 'user-create',
            self::UserDelete => 'user-delete',
            self::UserRevoke => 'user-revoke',
            
            self::UserProfileAction => 'user-profile',
            self::UserUploadAvatarAction => 'user-upload-avatar',
        };
    }
}
