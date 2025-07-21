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

        Centro::create([
            'nome' => 'MC1',
            'localizacao' => 'Vila de Viana',
            'contactos' => ['923456789'],
            'email' => 'vila@centro.ao',
        ]);

        Centro::create([
            'nome' => 'MC2',
            'localizacao' => 'Kimbango',
            'contactos' => ['924567890', '975678901'],
            'email' => 'kimbango@centro.ao',
        ]);

    }
}
