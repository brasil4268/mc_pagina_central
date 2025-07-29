<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categorias da Loja
        $categoriasLoja = [
            [
                'nome' => 'Computadores',
                'descricao' => 'Laptops, desktops e workstations',
                'tipo' => 'loja',
                'ativo' => true
            ],
            [
                'nome' => 'Acessórios',
                'descricao' => 'Mouses, teclados, monitores e periféricos',
                'tipo' => 'loja',
                'ativo' => true
            ],
            [
                'nome' => 'Material Escolar',
                'descricao' => 'Cadernos, canetas, calculadoras e material de escritório',
                'tipo' => 'loja',
                'ativo' => true
            ],
            [
                'nome' => 'Software',
                'descricao' => 'Software personalizado e soluções sob medida',
                'tipo' => 'loja',
                'ativo' => true
            ],
            [
                'nome' => 'Suporte Técnico',
                'descricao' => 'Serviços de manutenção e suporte',
                'tipo' => 'loja',
                'ativo' => true
            ]
        ];

        // Categorias do Snack Bar
        $categoriasSnack = [
            [
                'nome' => 'Bebidas Quentes',
                'descricao' => 'Café, chá e bebidas quentes',
                'tipo' => 'snack',
                'ativo' => true
            ],
            [
                'nome' => 'Bebidas Frias',
                'descricao' => 'Sumos, refrigerantes e águas',
                'tipo' => 'snack',
                'ativo' => true
            ],
            [
                'nome' => 'Comidas',
                'descricao' => 'Sandwiches, tostas e pratos ligeiros',
                'tipo' => 'snack',
                'ativo' => true
            ],
            [
                'nome' => 'Snacks',
                'descricao' => 'Petiscos, bolos e doces',
                'tipo' => 'snack',
                'ativo' => true
            ]
        ];

        foreach ($categoriasLoja as $categoria) {
            Categoria::create($categoria);
        }

        foreach ($categoriasSnack as $categoria) {
            Categoria::create($categoria);
        }
    }
}
