<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adresse extends BaseModel
{
    protected $table = 'adresses';

    protected $fillable = [
        'client_id',
        'libelle',
        'adresse_voie',
        'appartement',
        'ville',
        'region',
        'code_postal',
        'pays',
        'latitude',
        'longitude',
        'instructions_livraison',
        'est_par_defaut',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'est_par_defaut' => 'boolean',
    ];

    // ===== RELATIONS =====

    public function client(): BelongsTo
    {
        return $this->belongsTo(ProfilClient::class, 'client_id');
    }

    public function commandes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Commande::class, 'adresse_livraison_id');
    }

    // ===== SCOPES =====

    public function scopeParDefaut($query)
    {
        return $query->where('est_par_defaut', true);
    }

    // ===== MÉTHODES =====

    public function getAdresseCompleteAttribute(): string
    {
        $parts = array_filter([
            $this->adresse_voie,
            $this->appartement,
            $this->ville,
            $this->code_postal,
            $this->pays,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Définit cette adresse comme par défaut et retire le statut des autres
     */
    public function definirCommeDefaut(): void
    {
        self::where('client_id', $this->client_id)
            ->where('id', '!=', $this->id)
            ->update(['est_par_defaut' => false]);

        $this->update(['est_par_defaut' => true]);
    }
}
