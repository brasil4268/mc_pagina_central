<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curso;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        Curso::create([
            'centro_id' => 1,
            'nome' => 'Informática Básica',
            'descricao' => 'Curso introdutório de informática.',
            'programa' => 'Windows, Word, Excel, Internet',
            'duracao' => '3 meses',
            'preco' => 25000.00,
            'area' => 'Tecnologia',
            'modalidade' => 'presencial',
            'imagem_url' => null,
            'ativo' => true
        ]);

        Curso::create([
            'centro_id' => 2,
            'nome' => 'Gestão Empresarial',
            'descricao' => 'Curso de gestão para empreendedores.',
            'programa' => 'Administração, Finanças, Marketing',
            'duracao' => '4 meses',
            'preco' => 35000.00,
            'area' => 'Negócios',
            'modalidade' => 'online',
            'imagem_url' => null,
            'ativo' => true
        ]);
    }
}