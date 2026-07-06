<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickupSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'ordonnance_id',
        'proposed_at',
        'confirmed_at',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'proposed_at'      => 'datetime',
            'confirmed_at'     => 'datetime',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function ordonnance(): BelongsTo
    {
        return $this->belongsTo(Ordonnance::class);
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed_at !== null;
    }

    public function isPast(): bool
    {
        return $this->proposed_at->isPast();
    }
}
