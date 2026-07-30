<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalleRestaurant extends BaseModel
{
    protected $table = 'salles_restaurant';

    protected $fillable = [
        'restaurant_id',
        'nom',
        'description',
        'capacite',
        'est_privee',
        'nb_invites_min',
        'nb_invites_max',
    ];

    protected $casts = [
        'est_privee' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function tables(): HasMany
    {
        return $this->hasMany(TableRestaurant::class, 'salle_id');
    }
}
