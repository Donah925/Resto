<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class ProfilClient extends BaseModel
{
    protected $table = 'profils_client';

    protected $fillable = [
        'utilisateur_id',
        'points_fidelite',
        'total_depense',
        'nombre_commandes',
        'date_naissance',
        'preferences_alimentaires',
        'adresse_par_defaut_id',
        'opt_in_newsletter',
        'opt_in_sms',
        'opt_in_push',
        'code_parrainage',
        'parraine_par_id',
        'bonus_parrainage_reclame',
        'derniere_commande_le',
    ];

    protected $casts = [
        'points_fidelite' => 'integer',
        'total_depense' => 'decimal:2',
        'nombre_commandes' => 'integer',
        'date_naissance' => 'date',
        'preferences_alimentaires' => 'array',
        'opt_in_newsletter' => 'boolean',
        'opt_in_sms' => 'boolean',
        'opt_in_push' => 'boolean',
        'bonus_parrainage_reclame' => 'boolean',
        'derniere_commande_le' => 'datetime',
    ];

    protected $attributes = [
        'points_fidelite' => 0,
        'total_depense' => 0,
        'nombre_commandes' => 0,
    ];

    // ===== BOOT : Génération auto du code parrainage =====

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($client) {
            if (empty($client->code_parrainage)) {
                $client->code_parrainage = strtoupper(Str::random(8));
            }
        });
    }

    // ===== RELATIONS =====

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function adresses(): HasMany
    {
        return $this->hasMany(Adresse::class, 'client_id');
    }

    public function adresseParDefaut(): BelongsTo
    {
        return $this->belongsTo(Adresse::class, 'adresse_par_defaut_id');
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class, 'client_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'client_id');
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avi::class, 'client_id');
    }

    public function portefeuille(): HasOne
    {
        return $this->hasOne(Portefeuille::class, 'client_id');
    }

    public function transactionsFidelite(): HasMany
    {
        return $this->hasMany(TransactionFidelite::class, 'client_id');
    }

    public function favoris(): HasMany
    {
        return $this->hasMany(FavorisClient::class, 'client_id');
    }

    public function parrain(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parraine_par_id');
    }

    public function filleuls(): HasMany
    {
        return $this->hasMany(self::class, 'parraine_par_id');
    }

    // ===== SCOPES =====

    public function scopeFideles($query, int $pointsMin = 100)
    {
        return $query->where('points_fidelite', '>=', $pointsMin);
    }

    public function scopeActifs($query)
    {
        return $query->whereHas('utilisateur', fn($q) => $q->where('statut', 'actif'));
    }

    // ===== MÉTHODES =====

    public function ajouterPointsFidelite(int $points, string $description = null): void
    {
        $ancienSolde = $this->points_fidelite;
        $this->increment('points_fidelite', $points);

        $this->transactionsFidelite()->create([
            'type_transaction' => 'gain',
            'nombre_points' => $points,
            'solde_avant' => $ancienSolde,
            'solde_apres' => $this->points_fidelite,
            'description' => $description,
        ]);
    }

    public function utiliserPointsFidelite(int $points, string $description = null): bool
    {
        if ($this->points_fidelite < $points) {
            return false;
        }

        $ancienSolde = $this->points_fidelite;
        $this->decrement('points_fidelite', $points);

        $this->transactionsFidelite()->create([
            'type_transaction' => 'utilisation',
            'nombre_points' => -$points,
            'solde_avant' => $ancienSolde,
            'solde_apres' => $this->points_fidelite,
            'description' => $description,
        ]);

        return true;
    }

    public function getNiveauFideliteAttribute(): string
    {
        $points = $this->points_fidelite;

        return match(true) {
            $points >= 1000 => 'Platine',
            $points >= 500 => 'Or',
            $points >= 200 => 'Argent',
            $points >= 50 => 'Bronze',
            default => 'Standard',
        };
    }
}
