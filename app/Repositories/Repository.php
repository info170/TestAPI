<?php

namespace App\Repositories;

use App\Models\Hold;
use Illuminate\Support\Facades\DB;

class Repository implements RepositoryInterface
{
    public function findHoldByIdempotency(int $slotId, string $idempotencyKey): ?Hold
    {
        return Hold::query()
            ->where('slot_id', $slotId)
            ->where('idempotency_key', $idempotencyKey)
            ->first();
    }
    public function decrementSlotRemaining(int $slotId): int
    {
        return DB::table('slots')
            ->where('slot_id', $slotId)
            ->where('remaining', '>', 0)
            ->update([
                'remaining' => DB::raw('remaining - 1'),
            ]);
    }

    public function incrementSlotRemaining(int $slotId): int
    {
        return DB::table('slots')
            ->where('slot_id', $slotId)
            ->whereColumn('remaining', '<', 'capacity')
            ->update([
                'remaining' => DB::raw('remaining + 1')
            ]);
    }

}
