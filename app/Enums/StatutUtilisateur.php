<?php

namespace App\Enums;

enum StatutUtilisateur: string
{
    case ACTIF = 'actif';
    case INACTIF = 'inactif';
    case SUSPENDU = 'suspendu';
    case BANNI = 'banni';
}
