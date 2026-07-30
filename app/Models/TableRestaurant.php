<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableRestaurant extends BaseModel
{
    protected $table = 'tables_restaurant';

    protected $fillable = [
        'restaurant_id',
        'salle_id',
        'numero_table',
        'places',
        'est_disponible',
    ];

    protected $casts = [
        'est_disponible' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(SalleRestaurant::class, 'salle_id');
    }
}
