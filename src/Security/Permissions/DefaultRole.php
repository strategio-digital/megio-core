<?php

namespace Saas\Security\Permissions;

enum DefaultRole implements IRole
{
    case Admin;
    case Editor;
    case User;
    
    public function name(): string
    {
        return match ($this) {
            self::Admin => 'admin',
            self::Editor => 'editor',
            self::User => 'user'
        };
    }
}
