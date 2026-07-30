<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfigurationPaiement extends BaseModel
{
    protected $table = 'configurations_paiement';

    protected $fillable = [
        'restaurant_id',
        'prestataire',
        'est_active',
        'cles_api',
        'parametres',
        'webhook_secret',
    ];

    protected $casts = [
        'est_active' => 'boolean',
        'cles_api' => 'encrypted:array',
        'parametres' => 'array',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function scopeActives($query)
    {
        return $query->where('est_active', true);
    }

    public function scopePrestataire($query, string $prestataire)
    {
        return $query->where('prestataire', $prestataire);
    }
}
