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
        ['nome' => 'Centro Alpha', 'localizacao' => 'Luanda', 'contactos' => json_encode(['923111111']), 'email' => 'alpha@centro.com', 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'Centro Beta', 'localizacao' => 'Benguela', 'contactos' => json_encode(['923222222']), 'email' => 'beta@centro.com', 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'Centro Gama', 'localizacao' => 'Huambo', 'contactos' => json_encode(['923333333']), 'email' => 'gama@centro.com', 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'Centro Delta', 'localizacao' => 'Lubango', 'contactos' => json_encode(['923444444']), 'email' => 'delta@centro.com', 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'Centro Epsilon', 'localizacao' => 'Namibe', 'contactos' => json_encode(['923555555']), 'email' => 'epsilon@centro.com', 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'Centro Zeta', 'localizacao' => 'Cabinda', 'contactos' => json_encode(['923666666']), 'email' => 'zeta@centro.com', 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'Centro Eta', 'localizacao' => 'Uíge', 'contactos' => json_encode(['923777777']), 'email' => 'eta@centro.com', 'created_at' => $now, 'updated_at' => $now],
    ]);

    }
}
