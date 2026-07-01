<?php

namespace App\Http\Controllers;

use App\Exceptions\HoldNotFoundException;
use App\Exceptions\SlotIsFullException;
use App\Http\Requests\ConfirmHoldRequest;
use App\Services\SlotService;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class HoldController extends Controller
{
    public function __construct(private readonly SlotService $slotService)
    {
    }

    /**
     * @throws HoldNotFoundException
     * @throws SlotIsFullException
     */
    public function confirmHold(ConfirmHoldRequest $request): \App\Models\Hold
    {
        return $this->slotService->confirmHold($request->id);
    }

    /**
     * @throws HoldNotFoundException
     */
    public function deleteHold($id): \Illuminate\Database\Eloquent\Collection
    {
        return $this->slotService->cancelHold($id);
    }
}
