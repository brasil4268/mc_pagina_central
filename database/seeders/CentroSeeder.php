<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Centro;
use Illuminate\Database\Seeder;

class CentroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        // Centro::create([
        //     'nome' => 'MC4',
        //     'localizacao' => 'Vila de Viana',
        //     'contactos' => ['923456789'],
        //     'email' => 'vila@centro.ao',
        // ]);

        // Centro::create([
        //     'nome' => 'MC5',
        //     'localizacao' => 'Kimbango',
        //     'contactos' => ['924567890', '975678901'],
        //     'email' => 'kimbango@centro.ao',
        // ]);

    $now = now();
    Centro::insert([
        ['nome' => 'MC1', 'localizacao' => 'Luanda', 'contactos' => json_encode(['923111111']), 'email' => 'alpha@centro.com', 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'MC2', 'localizacao' => 'Benguela', 'contactos' => json_encode(['923222222']), 'email' => 'beta@centro.com', 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'MC3', 'localizacao' => 'Huambo', 'contactos' => json_encode(['923333333']), 'email' => 'gama@centro.com', 'created_at' => $now, 'updated_at' => $now],
    ]);

    }
}
