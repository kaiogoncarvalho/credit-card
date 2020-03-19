<?php

namespace App\Enums;

class Scope extends Enum
{
    const ADMIN = 'admin';
    const USER = 'user';
    
    public static function getScopes(): array
    {
        return [
            'admin' => 'Admin Users',
            'user' => 'Common Users',
        ];
    }
}
