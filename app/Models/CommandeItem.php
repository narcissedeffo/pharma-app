<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_id',
        'produit_id',
        'nom_medicament',
        'quantite',
        'prix_unitaire'
    ];

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function commande(): BelongsTo
    {
        return $this->belongsTo(CommandeFournisseur::class, 'commande_id');
    }
}
