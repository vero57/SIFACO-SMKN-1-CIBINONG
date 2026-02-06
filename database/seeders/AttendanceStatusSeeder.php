<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attendance_statuses')->insert([
            ['name' => 'Hadir'],
            ['name' => 'Sakit'],
            ['name' => 'Izin'],
            ['name' => 'Alfa'],
            ['name' => 'Telat'],
        ]);
    }
}
