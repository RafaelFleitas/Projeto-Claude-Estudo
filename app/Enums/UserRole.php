<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case User  = 'user';

    public function label(): string
    {
        return match ($this) {
            UserRole::Admin => 'Administrador',
            UserRole::User  => 'Usuário',
        };
    }
}
