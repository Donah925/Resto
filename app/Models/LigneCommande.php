<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneCommande extends BaseModel
{
    protected $table = 'lignes_commande';

    protected $fillable = [
        'commande_id',
        'produit_id',
        'option_id',
        'quantite',
        'prix_unitaire',
        'prix_total',
        'personnalisation',
        'notes',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'prix_total' => 'decimal:2',
        'personnalisation' => 'array',
    ];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(OptionProduit::class, 'option_id');
    }
}
