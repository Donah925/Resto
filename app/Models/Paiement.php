<?php

namespace App\Models;

use App\Enums\MethodePaiement;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends BaseModel
{
    protected $table = 'paiements';

    protected $fillable = [
        'commande_id',
        'client_id',
        'restaurant_id',
        'methode',
        'montant',
        'devise',
        'statut',
        'reference_transaction',
        'donnees_paiement',
        'frais_transaction',
        'net_a_verser',
        'date_paiement',
        'date_verification',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'frais_transaction' => 'decimal:2',
        'net_a_verser' => 'decimal:2',
        'donnees_paiement' => 'array',
        'date_paiement' => 'datetime',
        'date_verification' => 'datetime',
        'methode' => MethodePaiement::class,
    ];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(ProfilClient::class, 'client_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
