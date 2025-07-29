<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;
use App\Models\Categoria;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar se existem categorias
        $categoria = Categoria::first();
        if (!$categoria) {
            // Criar categoria de teste se não existir
            $categoria = Categoria::create([
                'nome' => 'Loja Geral',
                'descricao' => 'Produtos da loja',
                'tipo' => 'loja'
            ]);
        }

        $produtos = [
            [
                'nome' => 'Produto Teste 1',
                'descricao' => 'Descrição do produto teste 1',
                'preco' => 1500.00,
                'categoria_id' => $categoria->id,
                'ativo' => true,
                'em_destaque' => true,
            ],
            [
                'nome' => 'Produto Teste 2',
                'descricao' => 'Descrição do produto teste 2',
                'preco' => 2500.00,
                'categoria_id' => $categoria->id,
                'ativo' => true,
                'em_destaque' => false,
            ],
            [
                'nome' => 'Produto Teste 3',
                'descricao' => 'Descrição do produto teste 3',
                'preco' => 3500.00,
                'categoria_id' => $categoria->id,
                'ativo' => false,
                'em_destaque' => false,
            ]
        ];

        foreach ($produtos as $produto) {
            Produto::create($produto);
        }
    }
}
