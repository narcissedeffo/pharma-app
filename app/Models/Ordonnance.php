<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ordonnance extends Model
{
    use HasFactory, SoftDeletes;

    // Statuts étendus (inclut les nouveaux statuts fonctionnels)
    const STATUTS = ['brouillon', 'en_attente', 'en_cours', 'validee', 'refusee', 'expiree', 'retiree'];

    protected $fillable = [
        'client_id',
        'pharmacien_id',
        'original_filename',
        'file_path',
        'mime_type',
        'file_size',
        'status',
        'note_pharmacien',
        'published_at',
        'expires_at',
        'rating',
        'rating_comment',
        'rated_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'expires_at'   => 'date',
            'rated_at'     => 'datetime',
        ];
    }

    /* ─── Relations ─── */

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function pharmacien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pharmacien_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrdonnanceHistory::class)->latest();
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrdonnanceItem::class);
    }

    public function pickupSlot(): HasOne
    {
        return $this->hasOne(PickupSlot::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /* ─── Ownership checks ─── */

    public function isOwnedBy(User $user): bool
    {
        return $this->client_id === $user->id;
    }

    public function isAssignedTo(User $user): bool
    {
        return $this->pharmacien_id === $user->id;
    }

    /* ─── Expiration ─── */

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function daysUntilExpiry(): ?int
    {
        if (!$this->expires_at) return null;
        return max(0, (int) now()->diffInDays($this->expires_at, false));
    }

    public function isExpiringSoon(): bool
    {
        $days = $this->daysUntilExpiry();
        return $days !== null && $days <= 15 && $days >= 0;
    }

    /* ─── Rating ─── */

    public function isRated(): bool
    {
        return $this->rating !== null;
    }

    public function canBeRated(): bool
    {
        return in_array($this->status, ['validee', 'retiree']) && !$this->isRated();
    }

    /* ─── Status workflow ─── */

    /**
     * Enregistre un changement de statut avec historique et notification.
     */
    public function moveTo(string $newStatus, User $actor, ?string $comment = null): void
    {
        $oldStatus = $this->status;

        $this->update(['status' => $newStatus]);

        $this->histories()->create([
            'user_id'     => $actor->id,
            'from_status' => $oldStatus,
            'to_status'   => $newStatus,
            'comment'     => $comment,
        ]);

        // Envoi de notification à l'autre partie
        $targetUser = $actor->id === $this->client_id ? $this->pharmacien : $this->client;

        if ($targetUser) {
            $message = match ($newStatus) {
                'en_attente' => "Nouvelle ordonnance reçue de {$actor->name}.",
                'en_cours'   => "Votre ordonnance est en cours de traitement par {$actor->name}.",
                'validee'    => "🎉 Bonne nouvelle, votre ordonnance a été validée par {$actor->name} !",
                'refusee'    => "Votre ordonnance a été refusée. Consultez la note du pharmacien.",
                'retiree'    => "Retrait confirmé. N'oubliez pas de noter votre pharmacien !",
                'expiree'    => "Votre ordonnance a expiré.",
                default      => "Le statut de votre ordonnance a été mis à jour : {$this->statusLabel()}.",
            };

            $color = match ($newStatus) {
                'en_attente' => 'yellow',
                'en_cours'   => 'blue',
                'validee'    => 'green',
                'refusee'    => 'red',
                'retiree'    => 'teal',
                'expiree'    => 'gray',
                default      => 'gray',
            };

            $targetUser->notify(new \App\Notifications\OrdonnanceStatusChanged($this, $message, $color));
        }
    }

    /* ─── Labels & colors ─── */

    public function statusLabel(): string
    {
        return match ($this->status) {
            'brouillon'  => 'Brouillon',
            'en_attente' => 'En attente',
            'en_cours'   => 'En cours de traitement',
            'validee'    => 'Validée',
            'refusee'    => 'Refusée',
            'expiree'    => 'Expirée',
            'retiree'    => 'Retirée',
            default      => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'brouillon'  => 'bg-gray-100 text-gray-800',
            'en_attente' => 'bg-yellow-100 text-yellow-800',
            'en_cours'   => 'bg-blue-100 text-blue-800',
            'validee'    => 'bg-green-100 text-green-800',
            'refusee'    => 'bg-red-100 text-red-800',
            'expiree'    => 'bg-gray-200 text-gray-600',
            'retiree'    => 'bg-teal-100 text-teal-800',
            default      => 'bg-gray-100 text-gray-800',
        };
    }
}
