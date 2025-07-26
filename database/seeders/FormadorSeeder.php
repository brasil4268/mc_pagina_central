<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Formador;

class FormadorSeeder extends Seeder
{
    public function run(): void
    {
        // $formador = Formador::create([
        //     'nome' => 'Ana Silva',
        //     'email' => 'ana@formador.com',
        //     'contactos' => ['923456789'],
        //     'especialidade' => 'Informática',
        //     'bio' => 'Formadora experiente em tecnologia, Licenciada na UAN no Curso de Engenharia Informática',
        //     'foto_url' => null
        // ]);


        // // Relacionar com centros e cursos (exemplo)
        // $formador->centros()->attach([1]); // IDs dos centros
        // $formador->cursos()->attach([1]);    // IDs dos cursos

        // $formador = Formador::create([
        //     'nome' => 'Osvaldo Cazola',
        //     'email' => 'osvaldo@formador.com',
        //     'contactos' => ['922456730', "953524242"],
        //     'especialidade' => 'Ciências Exactas',
        //     'bio' => 'Formador experiente em ciências exactas, Licenciado na UAN no Curso de Engenharia Química',
        //     'foto_url' => null
        // ]);




        $now = now();
        Formador::insert([
            ['nome' => 'Ana Silva', 'email' => 'ana@formador.com', 'contactos' => json_encode(['923888888']), 'especialidade' => 'Informática', 'bio' => 'Especialista em informática.', 'foto_url' => null, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Bruno Costa', 'email' => 'bruno@formador.com', 'contactos' => json_encode(['923999999']), 'especialidade' => 'Gestão', 'bio' => 'Gestor experiente.', 'foto_url' => null, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Carla Dias', 'email' => 'carla@formador.com', 'contactos' => json_encode(['924111111']), 'especialidade' => 'Inglês', 'bio' => 'Professora de inglês.', 'foto_url' => null, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Daniel Rocha', 'email' => 'daniel@formador.com', 'contactos' => json_encode(['924222222']), 'especialidade' => 'Matemática', 'bio' => 'Matemático.', 'foto_url' => null, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Eduarda Lima', 'email' => 'eduarda@formador.com', 'contactos' => json_encode(['924333333']), 'especialidade' => 'Programação', 'bio' => 'Desenvolvedora web.', 'foto_url' => null, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Fernando Pinto', 'email' => 'fernando@formador.com', 'contactos' => json_encode(['924444444']), 'especialidade' => 'Contabilidade', 'bio' => 'Contabilista.', 'foto_url' => null, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Gabriela Torres', 'email' => 'gabriela@formador.com', 'contactos' => json_encode(['924555555']), 'especialidade' => 'Design', 'bio' => 'Designer gráfico.', 'foto_url' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);

        //         // Relacionar com centros e cursos (exemplo)
        // $formador->centros()->attach([2]); // IDs dos centros
        // $formador->cursos()->attach([1]);    // IDs dos cursos
    }
}