<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facture extends BaseModel
{
    protected $table = 'factures';

    protected $fillable = [
        'numero',
        'commande_id',
        'client_id',
        'restaurant_id',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'taux_tva',
        'chemin_pdf',
        'statut',
        'date_emission',
        'date_echeance',
    ];

    protected $casts = [
        'montant_ht' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
        'taux_tva' => 'decimal:2',
        'date_emission' => 'date',
        'date_echeance' => 'date',
    ];

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($facture) {
            if (empty($facture->numero)) {
                $annee = now()->format('Y');
                $count = self::whereYear('cree_le', now()->year)->count() + 1;
                $facture->numero = 'FAC-' . $annee . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }
        });
    }

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

    public function scopeEmises($query)
    {
        return $query->where('statut', 'emise');
    }

    public function scopePayees($query)
    {
        return $query->where('statut', 'payee');
    }
}
