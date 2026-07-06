<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdonnanceHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ordonnance_id',
        'user_id',
        'from_status',
        'to_status',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function ordonnance(): BelongsTo
    {
        return $this->belongsTo(Ordonnance::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
