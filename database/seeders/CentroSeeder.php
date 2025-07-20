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
            'contacto' => '923456789',
            'email' => 'vila@centro.ao',
        ]);

        Centro::create([
            'nome' => 'MC2',
            'localizacao' => 'Kimbango',
            'contacto' => '924567890',
            'email' => 'kimbango@centro.ao',
        ]);

    }
}
