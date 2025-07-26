<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PreInscricao;

class PreInscricaoSeeder extends Seeder
{
    public function run(): void
    {
        // PreInscricao::create([
        //     'curso_id' => 1,
        //     'centro_id' => 1,
        //     'horario_id' => 1,
        //     'nome_completo' => 'João Pedro',
        //     'contactos' => ['923456789'],
        //     'email' => 'joao@email.com',
        //     'status' => 'pendente',
        //     'observacoes' => 'Quero estudar à noite.'
        // ]);

        // PreInscricao::create([
        //     'curso_id' => 2,
        //     'centro_id' => 2,
        //     'horario_id' => 3,
        //     'nome_completo' => 'Maria Luísa',
        //     'contactos' => ['951456700'],
        //     'email' => 'maria@email.com',
        //     'status' => 'confirmado',
        //     'observacoes' => null
        // ]);

        $now = now();
        PreInscricao::insert([
            ['curso_id' => 1, 'centro_id' => 1, 'horario_id' => 1, 'nome_completo' => 'João Pedro', 'contactos' => json_encode(['923111111']), 'email' => 'joao@teste.com', 'status' => 'pendente', 'observacoes' => 'Quero estudar à tarde.', 'created_at' => $now, 'updated_at' => $now],
            ['curso_id' => 2, 'centro_id' => 2, 'horario_id' => 2, 'nome_completo' => 'Maria Luísa', 'contactos' => json_encode(['923222222']), 'email' => 'maria@teste.com', 'status' => 'confirmado', 'observacoes' => null, 'created_at' => $now, 'updated_at' => $now],
            ['curso_id' => 3, 'centro_id' => 3, 'horario_id' => 3, 'nome_completo' => 'Carlos Pinto', 'contactos' => json_encode(['923333333']), 'email' => 'carlos@teste.com', 'status' => 'pendente', 'observacoes' => 'Preferência para manhã.', 'created_at' => $now, 'updated_at' => $now],
            ['curso_id' => 4, 'centro_id' => 4, 'horario_id' => 4, 'nome_completo' => 'Ana Paula', 'contactos' => json_encode(['923444444']), 'email' => 'ana@teste.com', 'status' => 'pendente', 'observacoes' => null, 'created_at' => $now, 'updated_at' => $now],
            ['curso_id' => 5, 'centro_id' => 5, 'horario_id' => 5, 'nome_completo' => 'Bruno Dias', 'contactos' => json_encode(['923555555']), 'email' => 'bruno@teste.com', 'status' => 'pendente', 'observacoes' => 'Quero estudar online.', 'created_at' => $now, 'updated_at' => $now],
            ['curso_id' => 6, 'centro_id' => 6, 'horario_id' => 6, 'nome_completo' => 'Carla Torres', 'contactos' => json_encode(['923666666']), 'email' => 'carla@teste.com', 'status' => 'pendente', 'observacoes' => null, 'created_at' => $now, 'updated_at' => $now],
            ['curso_id' => 7, 'centro_id' => 7, 'horario_id' => 7, 'nome_completo' => 'Daniel Lima', 'contactos' => json_encode(['923777777']), 'email' => 'daniel@teste.com', 'status' => 'pendente', 'observacoes' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}