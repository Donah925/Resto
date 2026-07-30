<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoraireRestaurant extends BaseModel
{
    protected $table = 'horaires_restaurant';

    protected $fillable = [
        'restaurant_id',
        'jour_semaine',
        'heure_ouverture',
        'heure_fermeture',
        'est_ferme',
    ];

    protected $casts = [
        'est_ferme' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function getJourNomAttribute(): string
    {
        $jours = [
            0 => 'Dimanche',
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
        ];

        return $jours[$this->jour_semaine] ?? '';
    }
}
