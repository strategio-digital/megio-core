<?php

namespace Saas\Security\Permissions;

enum DefaultResource implements IResource
{
    case UserProfileAction;
    case UserUploadAvatarAction;
    
    case UserShowAll;
    case UserShowOne;
    case UserCreate;
    case UserDelete;
    
    public function name(): string
    {
        return match ($this) {
            self::UserProfileAction => 'user-profile-action',
            self::UserUploadAvatarAction => 'user-upload-avatar-action',
            self::UserShowAll => 'user-show-all-action',
            self::UserShowOne => 'user-show-one-action',
            self::UserCreate => 'user-create-action',
            self::UserDelete => 'user-delete-action',
        };
    }
}
