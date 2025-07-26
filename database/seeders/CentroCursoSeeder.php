<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CentroCursoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        DB::table('centro_curso')->insert([
            ['centro_id' => 1, 'curso_id' => 1, 'preco' => 10000, 'duracao' => '2 meses', 'created_at' => $now, 'updated_at' => $now],
            ['centro_id' => 2, 'curso_id' => 2, 'preco' => 15000, 'duracao' => '3 meses', 'created_at' => $now, 'updated_at' => $now],
            ['centro_id' => 3, 'curso_id' => 3, 'preco' => 12000, 'duracao' => '4 meses', 'created_at' => $now, 'updated_at' => $now],
            ['centro_id' => 4, 'curso_id' => 4, 'preco' => 11000, 'duracao' => '2 meses', 'created_at' => $now, 'updated_at' => $now],
            ['centro_id' => 5, 'curso_id' => 5, 'preco' => 18000, 'duracao' => '3 meses', 'created_at' => $now, 'updated_at' => $now],
            ['centro_id' => 6, 'curso_id' => 6, 'preco' => 13000, 'duracao' => '2 meses', 'created_at' => $now, 'updated_at' => $now],
            ['centro_id' => 7, 'curso_id' => 7, 'preco' => 16000, 'duracao' => '2 meses', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}