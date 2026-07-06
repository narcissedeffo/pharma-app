<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommandeFournisseur extends Model
{
    use HasFactory;

    protected $table = 'commandes_fournisseurs';

    protected $fillable = [
        'pharmacien_id',
        'fournisseur_id',
        'reference',
        'status',
        'notes',
        'sent_at'
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($commande) {
            if (empty($commande->reference)) {
                $commande->reference = 'CMD-' . strtoupper(uniqid());
            }
        });
    }

    public function pharmacien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pharmacien_id');
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fournisseur_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CommandeItem::class, 'commande_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CommandeMessage::class, 'commande_id');
    }

    public function facture()
    {
        return $this->hasOne(Facture::class, 'commande_id');
    }

    public function total(): float
    {
        return $this->items->sum(function ($item) {
            return ($item->prix_unitaire ?? 0) * $item->quantite;
        });
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'brouillon' => 'Brouillon',
            'envoyee' => 'Envoyée',
            'en_preparation' => 'En préparation',
            'expediee' => 'Expédiée',
            'livree' => 'Livrée',
            default => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'brouillon' => 'bg-gray-100 text-gray-800',
            'envoyee' => 'bg-blue-100 text-blue-800',
            'en_preparation' => 'bg-yellow-100 text-yellow-800',
            'expediee' => 'bg-purple-100 text-purple-800',
            'livree' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
