<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilisationCodePromo extends BaseModel
{
    protected $table = 'utilisations_codes_promo';

    protected $fillable = [
        'code_promo_id',
        'utilisateur_id',
        'commande_id',
        'date_utilisation',
        'montant_reduction',
    ];

    protected $casts = [
        'date_utilisation' => 'datetime',
        'montant_reduction' => 'decimal:2',
    ];

    public function codePromo(): BelongsTo
    {
        return $this->belongsTo(CodePromo::class, 'code_promo_id');
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }
}
