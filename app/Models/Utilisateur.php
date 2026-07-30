<?php

namespace App\Models;

use App\Enums\RoleUtilisateur;
use App\Enums\StatutUtilisateur;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Utilisateur extends Authenticatable
{
    use HasUuids, Notifiable, HasApiTokens, HasRoles, SoftDeletes;

    protected $table = 'utilisateurs';

    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'cree_le';
    const UPDATED_AT = 'modifie_le';
    const DELETED_AT = 'supprime_le';

    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'telephone',
        'mot_de_passe',
        'role',
        'avatar',
        'statut',
        'derniere_connexion_le',
        'langue',
        'fuseau_horaire',
        'photo_profil_chemin',
    ];

    protected $hidden = [
        'mot_de_passe',
        'token_remember',
        'deux_facteur_secret',
        'deux_facteur_codes_recuperation',
    ];

    protected $casts = [
        'email_verifie_le' => 'datetime',
        'telephone_verifie_le' => 'datetime',
        'derniere_connexion_le' => 'datetime',
        'cree_le' => 'datetime',
        'modifie_le' => 'datetime',
        'supprime_le' => 'datetime',
        'role' => RoleUtilisateur::class,
        'statut' => StatutUtilisateur::class,
    ];

    // ===== ATTRIBUTS ACCESSORS =====

    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->nom_complet) . '&background=random';
    }

    // ===== RELATIONS PROFILS =====

    public function profilSuperAdmin(): HasOne
    {
        return $this->hasOne(ProfilSuperAdmin::class, 'utilisateur_id');
    }

    public function profilAdmin(): HasOne
    {
        return $this->hasOne(ProfilAdmin::class, 'utilisateur_id');
    }

    public function profilGerant(): HasOne
    {
        return $this->hasOne(ProfilGerant::class, 'utilisateur_id');
    }

    public function profilLivreur(): HasOne
    {
        return $this->hasOne(ProfilLivreur::class, 'utilisateur_id');
    }

    public function profilClient(): HasOne
    {
        return $this->hasOne(ProfilClient::class, 'utilisateur_id');
    }

    /**
     * Retourne le profil selon le rôle
     */
    public function getProfilAttribute()
    {
        return match($this->role) {
            RoleUtilisateur::SUPERADMIN => $this->profilSuperAdmin,
            RoleUtilisateur::ADMIN => $this->profilAdmin,
            RoleUtilisateur::GERANT => $this->profilGerant,
            RoleUtilisateur::LIVREUR => $this->profilLivreur,
            RoleUtilisateur::CLIENT => $this->profilClient,
            default => null,
        };
    }

    // ===== SCOPES =====

    public function scopeActifs($query)
    {
        return $query->where('statut', StatutUtilisateur::ACTIF);
    }

    public function scopeRole($query, RoleUtilisateur $role)
    {
        return $query->where('role', $role);
    }

    public function scopeClients($query)
    {
        return $query->where('role', RoleUtilisateur::CLIENT);
    }

    public function scopeLivreurs($query)
    {
        return $query->where('role', RoleUtilisateur::LIVREUR);
    }

    // ===== MÉTHODES UTILITAIRES =====

    public function estActif(): bool
    {
        return $this->statut === StatutUtilisateur::ACTIF;
    }

    public function estClient(): bool
    {
        return $this->role === RoleUtilisateur::CLIENT;
    }

    public function estGerant(): bool
    {
        return $this->role === RoleUtilisateur::GERANT;
    }

    public function estAdmin(): bool
    {
        return in_array($this->role, [RoleUtilisateur::ADMIN, RoleUtilisateur::SUPERADMIN]);
    }

    public function estSuperAdmin(): bool
    {
        return $this->role === RoleUtilisateur::SUPERADMIN;
    }
}
