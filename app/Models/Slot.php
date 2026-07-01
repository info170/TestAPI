<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slot extends Model
{
    protected $table = 'slots';
    protected $primaryKey = 'slot_id';

    protected $fillable = [
        'capacity',
        'remaining',
        'version',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'remaining' => 'integer',
        'version' => 'integer',
    ];

    public function holds(): HasMany
    {
        return $this->hasMany(Hold::class, 'slot_id', 'slot_id');
    }

}
