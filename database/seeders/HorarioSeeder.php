<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Horario;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {

        $now = now();
        Horario::insert([
            ['curso_id' => 1, 'centro_id' => 1, 'dia_semana' => 'Segunda', 'periodo' => 'manhã', 'hora_inicio' => '08:00', 'hora_fim' => '10:00', 'created_at' => $now, 'updated_at' => $now],
            ['curso_id' => 2, 'centro_id' => 1, 'dia_semana' => 'Terça', 'periodo' => 'tarde', 'hora_inicio' => '14:00', 'hora_fim' => '16:00', 'created_at' => $now, 'updated_at' => $now],
            ['curso_id' => 3, 'centro_id' => 1, 'dia_semana' => 'Quarta', 'periodo' => 'noite', 'hora_inicio' => '18:00', 'hora_fim' => '20:00', 'created_at' => $now, 'updated_at' => $now],
            ['curso_id' => 4, 'centro_id' => 1, 'dia_semana' => 'Quinta', 'periodo' => 'manhã', 'hora_inicio' => '09:00', 'hora_fim' => '11:00', 'created_at' => $now, 'updated_at' => $now],
            ['curso_id' => 5, 'centro_id' => 1, 'dia_semana' => 'Sexta', 'periodo' => 'tarde', 'hora_inicio' => '15:00', 'hora_fim' => '17:00', 'created_at' => $now, 'updated_at' => $now],
            ['curso_id' => 6, 'centro_id' => 1, 'dia_semana' => 'Sábado', 'periodo' => 'manhã', 'hora_inicio' => '08:30', 'hora_fim' => '10:30', 'created_at' => $now, 'updated_at' => $now],
            ['curso_id' => 7, 'centro_id' => 1, 'dia_semana' => 'Domingo', 'periodo' => 'noite', 'hora_inicio' => '19:00', 'hora_fim' => '21:00', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}