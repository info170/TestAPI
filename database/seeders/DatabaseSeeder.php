<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [];
        for ($i = 1; $i <= 10; $i++) {
            $capacity = mt_rand(1, 10);
            $slots[] = [
                'capacity' => $capacity,
                'remaining' => $capacity,
            ];
        }
        DB::table('slots')->insert($slots);

        $this->command->info("Создано " . count($slots) . " слотов на ". DB::table('slots')->sum('capacity')." мест!");
    }

}
