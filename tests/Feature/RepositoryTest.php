<?php

namespace Tests\Feature;

use App\Repositories\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_increment_slot_remaining_does_not_exceed_capacity(): void
    {
        $slotId = DB::table('slots')->insertGetId([
            'capacity' => 1,
            'remaining' => 1,
        ]);

        $updatedRows = app(Repository::class)->incrementSlotRemaining($slotId);

        $this->assertSame(0, $updatedRows);
        $this->assertDatabaseHas('slots', [
            'slot_id' => $slotId,
            'capacity' => 1,
            'remaining' => 1,
        ]);
    }
}
