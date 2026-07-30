<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FermetureSpecialeRestaurant extends BaseModel
{
    protected $table = 'fermetures_speciales_restaurant';

    protected $fillable = [
        'restaurant_id',
        'titre',
        'raison',
        'date_debut',
        'date_fin',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
