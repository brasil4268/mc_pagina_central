<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CentroFormadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $now = now();
        DB::table('centro_formador')->insert([
            ['centro_id' => 1, 'formador_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['centro_id' => 2, 'formador_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['centro_id' => 3, 'formador_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['centro_id' => 2, 'formador_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['centro_id' => 2, 'formador_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['centro_id' => 3, 'formador_id' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['centro_id' => 3, 'formador_id' => 7, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
