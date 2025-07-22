<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Formador;

class FormadorSeeder extends Seeder
{
    public function run(): void
    {
        $formador = Formador::create([
            'nome' => 'Ana Silva',
            'email' => 'ana@formador.com',
            'contactos' => ['923456789'],
            'especialidade' => 'Informática',
            'bio' => 'Formadora experiente em tecnologia, Licenciada na UAN no Curso de Engenharia Informática',
            'foto_url' => null
        ]);


        // Relacionar com centros e cursos (exemplo)
        $formador->centros()->attach([1]); // IDs dos centros
        $formador->cursos()->attach([1]);    // IDs dos cursos

        $formador = Formador::create([
            'nome' => 'Osvaldo Cazola',
            'email' => 'osvaldo@formador.com',
            'contactos' => ['922456730', "953524242"],
            'especialidade' => 'Ciências Exactas',
            'bio' => 'Formador experiente em ciências exactas, Licenciado na UAN no Curso de Engenharia Química',
            'foto_url' => null
        ]);


        // Relacionar com centros e cursos (exemplo)
        $formador->centros()->attach([2]); // IDs dos centros
        $formador->cursos()->attach([1]);    // IDs dos cursos
    }
}