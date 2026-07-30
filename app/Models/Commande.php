<?php

namespace App\Models;

use App\Enums\StatutCommande;
use App\Enums\TypeCommande;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Commande extends BaseModel
{
    protected $table = 'commandes';

    protected $fillable = [
        'restaurant_id',
        'client_id',
        'livreur_id',
        'adresse_livraison_id',
        'numero',
        'type_commande',
        'statut',
        'sous_total',
        'reduction',
        'frais_livraison',
        'frais_service',
        'total_ttc',
        'montant_paye',
        'methode_paiement',
        'statut_paiement',
        'code_promo_id',
        'points_fidelite_utilises',
        'notes_client',
        'notes_restaurant',
        'date_prevue',
        'confirme_le',
        'en_preparation_le',
        'prete_le',
        'en_livraison_le',
        'livree_le',
        'annulee_le',
    ];

    protected $casts = [
        'sous_total' => 'decimal:2',
        'reduction' => 'decimal:2',
        'frais_livraison' => 'decimal:2',
        'frais_service' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'date_prevue' => 'datetime',
        'confirme_le' => 'datetime',
        'en_preparation_le' => 'datetime',
        'prete_le' => 'datetime',
        'en_livraison_le' => 'datetime',
        'livree_le' => 'datetime',
        'annulee_le' => 'datetime',
        'statut' => StatutCommande::class,
        'type_commande' => TypeCommande::class,
    ];

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($commande) {
            if (empty($commande->numero)) {
                $date = now()->format('Ymd');
                $count = self::whereDate('cree_le', today())->count() + 1;
                $commande->numero = 'CMD-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(ProfilClient::class, 'client_id');
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(ProfilLivreur::class, 'livreur_id');
    }

    public function adresseLivraison(): BelongsTo
    {
        return $this->belongsTo(Adresse::class, 'adresse_livraison_id');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneCommande::class, 'commande_id');
    }

    public function paiement(): HasOne
    {
        return $this->hasOne(Paiement::class, 'commande_id');
    }

    public function codePromo(): BelongsTo
    {
        return $this->belongsTo(CodePromo::class, 'code_promo_id');
    }

    public function avis(): HasOne
    {
        return $this->hasOne(Avi::class, 'commande_id');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', StatutCommande::EN_ATTENTE);
    }

    public function scopeEnCours($query)
    {
        return $query->whereIn('statut', [
            StatutCommande::CONFIRMEE,
            StatutCommande::EN_PREPARATION,
            StatutCommande::PRETE,
            StatutCommande::EN_LIVRAISON,
        ]);
    }

    public function scopeTerminees($query)
    {
        return $query->whereIn('statut', [
            StatutCommande::LIVREE,
            StatutCommande::TERMINEE,
        ]);
    }

    public function peutEtreAnnulee(): bool
    {
        return in_array($this->statut, [
            StatutCommande::EN_ATTENTE,
            StatutCommande::CONFIRMEE,
        ]);
    }
}
