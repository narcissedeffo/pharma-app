<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdonnanceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'ordonnance_id',
        'nom_medicament',
        'statut_disponibilite',
        'commentaire',
    ];

    public function ordonnance(): BelongsTo
    {
        return $this->belongsTo(Ordonnance::class);
    }

    public function statutLabel(): string
    {
        return match ($this->statut_disponibilite) {
            'disponible'  => 'Disponible',
            'a_commander' => 'À commander',
            'indisponible' => 'Indisponible',
            default        => $this->statut_disponibilite,
        };
    }

    public function statutColor(): string
    {
        return match ($this->statut_disponibilite) {
            'disponible'   => 'text-green-700 bg-green-100',
            'a_commander'  => 'text-amber-700 bg-amber-100',
            'indisponible' => 'text-red-700 bg-red-100',
            default        => 'text-gray-700 bg-gray-100',
        };
    }

    public function statutIcon(): string
    {
        return match ($this->statut_disponibilite) {
            'disponible'   => '✅',
            'a_commander'  => '⏳',
            'indisponible' => '❌',
            default        => '•',
        };
    }
}
