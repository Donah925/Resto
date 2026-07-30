<?php

namespace App\Enums;

enum MethodePaiement: string
{
    case CARTE_BANCAIRE = 'carte_bancaire';
    case MTN_MOBILE_MONEY = 'mtn_mobile_money';
    case MOOV_MONEY = 'moov_money';
    case ORANGE_MONEY = 'orange_money';
    case WAVE = 'wave';
    case PAYPAL = 'paypal';
    case PAYEER = 'payeer';
    case PAYONEER = 'payoneer';
    case PORTEFEUILLE = 'portefeuille';
    case CARTE_CADEAU = 'carte_cadeau';
    case ESPECES_LIVRAISON = 'especes_livraison';
    case VIREMENT = 'virement';
}
