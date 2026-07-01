<?php

namespace Tests\Feature;

use App\Models\Hold;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HoldIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_repeated_hold_request_with_same_key_returns_confirmed_hold(): void
    {
        $slotId = DB::table('slots')->insertGetId([
            'capacity' => 1,
            'remaining' => 1,
        ]);
        $idempotencyKey = '018fd7db-8fc5-7de6-8d4d-43cf3a9671f9';

        $holdId = $this->postJson("/slots/{$slotId}/hold", [], [
            'Idempotency-Key' => $idempotencyKey,
        ])
            ->assertSuccessful()
            ->assertJsonPath('status', Hold::STATUS_HELD)
            ->json('hold_id');

        $this->postJson("/holds/{$holdId}/confirm")
            ->assertSuccessful()
            ->assertJsonPath('status', Hold::STATUS_CONFIRMED);

        $this->postJson("/slots/{$slotId}/hold", [], [
            'Idempotency-Key' => $idempotencyKey,
        ])
            ->assertSuccessful()
            ->assertJsonPath('hold_id', $holdId)
            ->assertJsonPath('status', Hold::STATUS_CONFIRMED);
    }

}
