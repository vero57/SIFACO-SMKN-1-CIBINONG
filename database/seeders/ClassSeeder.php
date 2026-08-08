<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('classes')->insert([
            ['name' => 'X SIJA 1', 'walas_id' => 1],
            ['name' => 'XI DKV 2', 'walas_id' => 2],
            ['name' => 'XI DKV 1', 'walas_id' => 3],
            ['name' => 'XII SIJA 1', 'walas_id' => 4],
        ]);
    }
}
