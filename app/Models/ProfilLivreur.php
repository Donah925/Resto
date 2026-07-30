<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfilLivreur extends BaseModel
{
    protected $table = 'profils_livreur';

    protected $fillable = [
        'utilisateur_id',
        'type_vehicule',
        'immatriculation',
        'numero_permis',
        'note',
        'total_livraisons',
        'zone_geojson',
        'est_disponible',
        'latitude_courante',
        'longitude_courante',
        'derniere_maj_localisation',
        'rib_bancaire',
        'nom_banque',
        'titulaire_compte',
        'total_gains',
    ];

    protected $casts = [
        'note' => 'decimal:2',
        'total_livraisons' => 'integer',
        'zone_geojson' => 'array',
        'est_disponible' => 'boolean',
        'latitude_courante' => 'decimal:8',
        'longitude_courante' => 'decimal:8',
        'derniere_maj_localisation' => 'datetime',
        'total_gains' => 'decimal:2',
    ];

    protected $attributes = [
        'est_disponible' => false,
        'total_livraisons' => 0,
        'total_gains' => 0,
        'note' => 5.00,
    ];

    // ===== RELATIONS =====

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function livraisons(): HasMany
    {
        return $this->hasMany(Livraison::class, 'livreur_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(EvaluationLivreur::class, 'livreur_id');
    }

    public function planning(): HasMany
    {
        return $this->hasMany(PlanningLivreur::class, 'livreur_id');
    }

    // ===== SCOPES =====

    public function scopeDisponibles($query)
    {
        return $query->where('est_disponible', true);
    }

    public function scopeDansZone($query, $latitude, $longitude, $rayonKm = 10)
    {
        // Formule Haversine simplifiée pour MySQL
        return $query->whereRaw("
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude_courante)) *
                cos(radians(longitude_courante) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude_courante))
            )) <= ?
        ", [$latitude, $longitude, $latitude, $rayonKm]);
    }

    // ===== MÉTHODES =====

    public function mettreAJourLocalisation(float $lat, float $lng): self
    {
        $this->update([
            'latitude_courante' => $lat,
            'longitude_courante' => $lng,
            'derniere_maj_localisation' => now(),
        ]);

        return $this;
    }
}
