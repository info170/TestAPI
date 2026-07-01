<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hold extends Model
{
    const STATUS_HELD = 'held';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const HOLD_LIFETIME_MINUTES = 5;

    protected $table = 'holds';
    protected $primaryKey = 'hold_id';
    protected $fillable = [
        'slot_id',
        'idempotency_key',
        'status',
        'expires_at',
    ];

    protected $hidden = [
        'idempotency_key',
    ];

    protected $casts = [
        'slot_id' => 'integer',
        'expires_at' => 'datetime',
    ];


    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class, 'slot_id', 'slot_id');
    }

}
