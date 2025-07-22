<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Horario;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        Horario::create([
            'curso_id' => 1,
            'dia_semana' => 'Segunda',
            'periodo' => 'manhã',
            'hora_inicio' => '08:00',
            'hora_fim' => '10:00'
        ]);

        Horario::create([
            'curso_id' => 1,
            'dia_semana' => 'Quarta',
            'periodo' => 'tarde',
            'hora_inicio' => '14:00',
            'hora_fim' => '16:00'
        ]);

        Horario::create([
            'curso_id' => 2,
            'dia_semana' => 'Sábado',
            'periodo' => 'noite',
            'hora_inicio' => '18:00',
            'hora_fim' => '20:00'
        ]);
    }
}