<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Exceptions\SlotIsFullException;
use App\Models\Hold;
use App\Models\Slot;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SlotService
{
    const FRESH_SLOTS_DATA = 5;
    const EXPIRED_SLOTS_DATA = 15;

    const HOLD_EXPIRED = Hold::HOLD_LIFETIME_MINUTES * 60;

    public function __construct(private readonly Repository $repository)
    {
    }

    public function getAvailableSlots()
    {
        $interval = [self::FRESH_SLOTS_DATA, self::EXPIRED_SLOTS_DATA];

        return Cache::flexible('slots', $interval, function () {
            return Slot::query()
                ->where('capacity','>',0)
                ->get();
        });
    }

    public function addHold(int $slotId, string $idempotencyKey): Hold
    {
        try {
            return DB::transaction(function () use ($slotId, $idempotencyKey): Hold {

                $hold = $this->repository->findHoldByIdempotency($slotId, $idempotencyKey);
                if ($hold) {
                    return $hold;
                }

                $slot = Slot::query()
                    ->where('slot_id', $slotId)
                    ->lockForUpdate()
                    ->first();

                if (!$slot) {
                    throw new NotFoundException('Слот не найден');
                }

                if ($slot->remaining < 1) {
                    throw new SlotIsFullException('Мест нет');
                }

                return Hold::create([
                    'slot_id' => $slotId,
                    'status' => Hold::STATUS_HELD,
                    'expires_at' => Carbon::now()->addSeconds(self::HOLD_EXPIRED),
                    'idempotency_key' => $idempotencyKey,
                ]);
            });
        } catch (QueryException $exception) {
            $hold = $this->repository->findHoldByIdempotency($slotId, $idempotencyKey);

            if ($hold) {
                return $hold;
            }

            throw $exception;
        }
    }

    public function confirmHold(int $holdId): Hold
    {
        return DB::transaction(
        /**
         * @throws SlotIsFullException
         * @throws NotFoundException
         */
        function () use ($holdId): Hold {
            $hold = Hold::query()
                ->where('hold_id', $holdId)
                ->lockForUpdate()
                ->first();

            if (!$hold || $hold->status !== Hold::STATUS_HELD) {
                throw new NotFoundException('Открытый Холд не найден');
            }

            if ($hold->status === Hold::STATUS_CONFIRMED) {
                return $hold;
            }

            $slot = Slot::query()
                ->where('slot_id', $hold->slot_id)
                ->lockForUpdate()
                ->first();

            if ($slot->remaining < 1) {
                throw new SlotIsFullException('А мест уже нет...');
            }

            $this->repository->decrementSlotRemaining($slot->slot_id);

            $hold->status = Hold::STATUS_CONFIRMED;
            $hold->save();

            Cache::forget('slots');

            return $hold->refresh();
        });
    }

    public function cancelHold($holdId): Collection
    {
        return DB::transaction(
        /**
         * @throws NotFoundException
         */
            function () use ($holdId): Collection {
                $hold = Hold::query()
                    ->where('hold_id', $holdId)
                    ->where('status', Hold::STATUS_HELD)
                    ->lockForUpdate()
                    ->first();

                if (!$hold) {
                    throw new NotFoundException('Открытый Холд не найден или уже подтвержден');
                }

                $slot = Slot::query()
                    ->where('slot_id', $hold->slot_id)
                    ->lockForUpdate()
                    ->first();

                if ($slot->remaining < $slot->capacity) {
                    $this->repository->incrementSlotRemaining($slot->slot_id);
                }

                $hold->status = Hold::STATUS_CANCELLED;
                $hold->save();

                Cache::forget('slots');

                return $this->getAvailableSlots();
            });

    }

}
