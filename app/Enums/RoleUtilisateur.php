<?php

namespace App\Enums;

enum RoleUtilisateur: string
{
    case SUPERADMIN = 'superadmin';
    case ADMIN = 'admin';
    case GERANT = 'gerant';
    case LIVREUR = 'livreur';
    case CLIENT = 'client';

    public function label(): string
    {
        return match($this) {
            self::SUPERADMIN => 'Super Administrateur',
            self::ADMIN => 'Administrateur',
            self::GERANT => 'Gérant',
            self::LIVREUR => 'Livreur',
            self::CLIENT => 'Client',
        };
    }
}
