<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ViolationRuleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('violation_rules')->insert([
            ['name' => 'Terlambat', 'points' => 5],
            ['name' => 'Alfa', 'points' => 10],
            ['name' => 'Bolos', 'points' => 10],
            ['name' => 'Bawa HP saat jam pelajaran', 'points' => 3],
            ['name' => 'Tidak memakai seragam lengkap', 'points' => 2],
        ]);
    }
}
