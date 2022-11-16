<?php

namespace Saas\Security\Permissions;

enum DefaultRole implements IRole
{
    case Guest;
    case Admin;
    case Registered;
    
    public function name(): string
    {
        return match ($this) {
            self::Guest => 'guest',
            self::Admin => 'admin',
            self::Registered => 'registered'
        };
    }
}
