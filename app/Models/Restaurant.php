<?php

namespace App\Models;

use App\Traits\HasTranslations;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Restaurant extends SoftDeleteModel
{
    use HasTranslations, HasSlug;

    protected $table = 'restaurants';

    public array $translatable = ['nom', 'description'];

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'logo',
        'image_couverture',
        'telephone',
        'email',
        'site_web',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'latitude',
        'longitude',
        'fuseau_horaire',
        'devise',
        'taux_tva',
        'livraison_activee',
        'retrait_active',
        'sur_place_active',
        'montant_minimum_commande',
        'rayon_max_livraison',
        'temps_preparation',
        'note',
        'total_avis',
        'est_en_avant',
        'statut',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'taux_tva' => 'decimal:2',
        'livraison_activee' => 'boolean',
        'retrait_active' => 'boolean',
        'sur_place_active' => 'boolean',
        'montant_minimum_commande' => 'decimal:2',
        'note' => 'decimal:2',
        'total_avis' => 'integer',
        'est_en_avant' => 'boolean',
    ];

    // ===== RELATIONS =====

    public function gerant(): HasOne
    {
        return $this->hasOne(ProfilGerant::class, 'restaurant_id');
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(
            ProfilAdmin::class,
            'restaurant_admin',
            'restaurant_id',
            'admin_id'
        );
    }

    public function horaires(): HasMany
    {
        return $this->hasMany(HoraireRestaurant::class, 'restaurant_id');
    }

    public function fermeturesSpeciales(): HasMany
    {
        return $this->hasMany(FermetureSpecialeRestaurant::class, 'restaurant_id');
    }

    public function salles(): HasMany
    {
        return $this->hasMany(SalleRestaurant::class, 'restaurant_id');
    }

    public function tables(): HasMany
    {
        return $this->hasMany(TableRestaurant::class, 'restaurant_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Categorie::class, 'restaurant_id');
    }

    public function produits(): HasMany
    {
        return $this->hasManyThrough(Produit::class, Categorie::class, 'restaurant_id');
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class, 'restaurant_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'restaurant_id');
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avi::class, 'restaurant_id');
    }

    public function zonesLivraison(): HasMany
    {
        return $this->hasMany(ZoneLivraison::class, 'restaurant_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ImageRestaurant::class, 'restaurant_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(ServiceRestaurant::class, 'restaurant_id');
    }

    public function menusDuJour(): HasMany
    {
        return $this->hasMany(MenuDuJour::class, 'restaurant_id');
    }

    public function configurationsPaiement(): HasMany
    {
        return $this->hasMany(ConfigurationPaiement::class, 'restaurant_id');
    }

    // ===== SCOPES =====

    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeEnAvant($query)
    {
        return $query->where('est_en_avant', true);
    }

    public function scopeAvecLivraison($query)
    {
        return $query->where('livraison_activee', true);
    }

    public function scopeOuvertsMaintenant($query)
    {
        $now = now()->format('H:i:s');
        $jour = now()->dayOfWeek; // 0 = dimanche

        return $query->whereHas('horaires', function($q) use ($now, $jour) {
            $q->where('jour_semaine', $jour)
              ->where('est_ferme', false)
              ->where('heure_ouverture', '<=', $now)
              ->where('heure_fermeture', '>=', $now);
        });
    }

    public function scopeProchesDe($query, float $latitude, float $longitude, int $rayonKm = 10)
    {
        return $query->selectRaw("*,
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) AS distance
        ", [$latitude, $longitude, $latitude])
        ->having('distance', '<=', $rayonKm)
        ->orderBy('distance');
    }

    // ===== MÉTHODES =====

    public function estOuvertMaintenant(): bool
    {
        $now = now()->format('H:i:s');
        $jour = now()->dayOfWeek;

        // Vérifier fermetures spéciales
        $fermetureSpeciale = $this->fermeturesSpeciales()
            ->where('date_debut', '<=', now()->toDateString())
            ->where('date_fin', '>=', now()->toDateString())
            ->exists();

        if ($fermetureSpeciale) {
            return false;
        }

        return $this->horaires()
            ->where('jour_semaine', $jour)
            ->where('est_ferme', false)
            ->where('heure_ouverture', '<=', $now)
            ->where('heure_fermeture', '>=', $now)
            ->exists();
    }

    public function recalculerNote(): void
    {
        $note = $this->avis()->where('est_visible', true)->avg('note_globale') ?? 0;
        $total = $this->avis()->where('est_visible', true)->count();

        $this->update([
            'note' => round($note, 2),
            'total_avis' => $total,
        ]);
    }
}
