<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curso;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        // Curso::create([
        //     'centro_id' => 1,
        //     'nome' => 'Informática Básica',
        //     'descricao' => 'Curso introdutório de informática.',
        //     'programa' => 'Windows, Word, Excel, Internet',
        //     'duracao' => '3 meses',
        //     'preco' => 25000.00,
        //     'area' => 'Tecnologia',
        //     'modalidade' => 'presencial',
        //     'imagem_url' => null,
        //     'ativo' => true
        // ]);

        // Curso::create([
        //     'centro_id' => 2,
        //     'nome' => 'Gestão Empresarial',
        //     'descricao' => 'Curso de gestão para empreendedores.',
        //     'programa' => 'Administração, Finanças, Marketing',
        //     'duracao' => '4 meses',
        //     'preco' => 35000.00,
        //     'area' => 'Negócios',
        //     'modalidade' => 'online',
        //     'imagem_url' => null,
        //     'ativo' => true
        // ]);

        $now = now();
        Curso::insert([
        ['nome' => 'Informática Básica', 'descricao' => 'Curso de introdução à informática.', 'programa' => 'Windows, Word, Excel', 'area' => 'Tecnologia', 'modalidade' => 'presencial', 'ativo' => true, 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'Gestão Empresarial', 'descricao' => 'Curso de gestão.', 'programa' => 'Administração, RH', 'area' => 'Gestão', 'modalidade' => 'presencial', 'ativo' => true, 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'Inglês', 'descricao' => 'Curso de inglês básico.', 'programa' => 'Gramática, Conversação', 'area' => 'Idiomas', 'modalidade' => 'online', 'ativo' => true, 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'Matemática Aplicada', 'descricao' => 'Curso de matemática.', 'programa' => 'Álgebra, Estatística', 'area' => 'Ciências', 'modalidade' => 'presencial', 'ativo' => true, 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'Programação Web', 'descricao' => 'Curso de web.', 'programa' => 'HTML, CSS, JS', 'area' => 'Tecnologia', 'modalidade' => 'online', 'ativo' => true, 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'Contabilidade', 'descricao' => 'Curso de contabilidade.', 'programa' => 'Balanço, Fiscal', 'area' => 'Gestão', 'modalidade' => 'presencial', 'ativo' => true, 'created_at' => $now, 'updated_at' => $now],
        ['nome' => 'Design Gráfico', 'descricao' => 'Curso de design.', 'programa' => 'Photoshop, Illustrator', 'area' => 'Artes', 'modalidade' => 'online', 'ativo' => true, 'created_at' => $now, 'updated_at' => $now],
    ]);
    }
}