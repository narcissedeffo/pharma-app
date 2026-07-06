<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_id',
        'reference',
        'bl_reference',
        'montant_total',
        'date_emission',
        'date_echeance',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_emission' => 'date',
            'date_echeance' => 'date',
            'montant_total' => 'decimal:2',
        ];
    }

    public function commande(): BelongsTo
    {
        return $this->belongsTo(CommandeFournisseur::class, 'commande_id');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'en_attente' && $this->date_echeance->isPast();
    }
}
