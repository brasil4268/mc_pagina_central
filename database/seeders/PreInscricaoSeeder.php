<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PreInscricao;

class PreInscricaoSeeder extends Seeder
{
    public function run(): void
    {
        PreInscricao::create([
            'curso_id' => 1,
            'centro_id' => 1,
            'horario_id' => 1,
            'nome_completo' => 'João Pedro',
            'contactos' => ['923456789'],
            'email' => 'joao@email.com',
            'status' => 'pendente',
            'observacoes' => 'Quero estudar à noite.'
        ]);

        PreInscricao::create([
            'curso_id' => 2,
            'centro_id' => 2,
            'horario_id' => 3,
            'nome_completo' => 'Maria Luísa',
            'contactos' => ['951456700'],
            'email' => 'maria@email.com',
            'status' => 'confirmado',
            'observacoes' => null
        ]);
    }
}