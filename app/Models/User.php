<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'invite_token',
        'invite_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'invite_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'invite_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Ordonnances déposées par ce client.
     */
    public function ordonnancesClient(): HasMany
    {
        return $this->hasMany(Ordonnance::class, 'client_id');
    }

    /**
     * Ordonnances reçues par ce pharmacien.
     */
    public function ordonnancesPharmacien(): HasMany
    {
        return $this->hasMany(Ordonnance::class, 'pharmacien_id');
    }

    /**
     * Commandes fournisseur passées par ce pharmacien.
     */
    public function commandesPassees(): HasMany
    {
        return $this->hasMany(CommandeFournisseur::class, 'pharmacien_id');
    }

    /**
     * Commandes fournisseur reçues par ce fournisseur.
     */
    public function commandesRecues(): HasMany
    {
        return $this->hasMany(CommandeFournisseur::class, 'fournisseur_id');
    }

    /**
     * Produits proposés par ce fournisseur.
     */
    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class, 'fournisseur_id');
    }

    /**
     * Vérifie si l'utilisateur a l'un des rôles donnés (slugs).
     */
    public function hasRole(string ...$slugs): bool
    {
        return $this->role && in_array($this->role->slug, $slugs, true);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
