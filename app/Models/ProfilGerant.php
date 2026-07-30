<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfilGerant extends BaseModel
{
    protected $table = 'profils_gerant';

    protected $fillable = [
        'utilisateur_id',
        'restaurant_id',
        'salaire',
        'date_embauche',
        'taux_commission',
    ];

    protected $casts = [
        'date_embauche' => 'date',
        'salaire' => 'decimal:2',
        'taux_commission' => 'decimal:2',
    ];

    // ===== RELATIONS =====

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function reversements(): HasMany
    {
        return $this->hasMany(ReversementGerant::class, 'gerant_id');
    }

    // ===== SCOPES =====

    public function scopeRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }
}
