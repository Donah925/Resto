<?php

namespace App\Enums;

enum StatutCommande: string
{
    case EN_ATTENTE = 'en_attente';
    case CONFIRMEE = 'confirmee';
    case EN_PREPARATION = 'en_preparation';
    case PRETE = 'prete';
    case EN_LIVRAISON = 'en_livraison';
    case LIVREE = 'livree';
    case TERMINEE = 'terminee';
    case ANNULEE = 'annulee';
    case REMBOURSEE = 'remboursee';

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::CONFIRMEE => 'Confirmée',
            self::EN_PREPARATION => 'En préparation',
            self::PRETE => 'Prête',
            self::EN_LIVRAISON => 'En livraison',
            self::LIVREE => 'Livrée',
            self::TERMINEE => 'Terminée',
            self::ANNULEE => 'Annulée',
            self::REMBOURSEE => 'Remboursée',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'yellow',
            self::CONFIRMEE => 'blue',
            self::EN_PREPARATION => 'indigo',
            self::PRETE => 'cyan',
            self::EN_LIVRAISON => 'purple',
            self::LIVREE => 'green',
            self::TERMINEE => 'gray',
            self::ANNULEE => 'red',
            self::REMBOURSEE => 'orange',
        };
    }
}
