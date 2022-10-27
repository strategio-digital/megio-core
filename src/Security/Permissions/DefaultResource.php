<?php

namespace Framework\Security\Permissions;

enum DefaultResource implements IResource
{
    case UserProfileAction;
    case UserUploadAvatarAction;
    
    public function name(): string
    {
        return match ($this) {
            self::UserProfileAction => 'user-profile-action',
            self::UserUploadAvatarAction => 'user-upload-avatar-action',
        };
    }
}
