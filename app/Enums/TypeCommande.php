<?php

namespace App\Enums;

enum TypeCommande: string
{
    case LIVRAISON = 'livraison';
    case RETRAIT = 'retrait';
    case SUR_PLACE = 'sur_place';
}
