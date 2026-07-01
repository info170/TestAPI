<?php

namespace App\Http\Controllers;

use App\Exceptions\SlotIsFullException;
use App\Http\Requests\AddHoldRequest;
use App\Models\Hold;
use App\Services\SlotService;

class AvailabilityController extends Controller
{
    public function __construct(private readonly SlotService $slotService)
    {
    }

    public function getAvailableSlots() {
        return $this->slotService->getAvailableSlots();
    }

    /**
     * @throws SlotIsFullException
     */
    public function addHold(AddHoldRequest $request): Hold
    {
        $slotId = $request['id'];
        $idempotencyKey = $request['idempotency_key'];

        return $this->slotService->addHold($slotId, $idempotencyKey);
    }
}
