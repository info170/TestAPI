<?php

namespace App\Repositories;

use App\Models\Hold;

interface RepositoryInterface
{
    public function findHoldByIdempotency(int $slotId, string $idempotencyKey);
    public function decrementSlotRemaining(int $slotId);
    public function incrementSlotRemaining(int $slotId);
}