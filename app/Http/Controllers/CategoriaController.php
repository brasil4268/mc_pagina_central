<?php

/* ==============================================
   CONTROLLER: CATEGORIA
   DESCRIÇÃO: API REST para gestão de categorias de produtos (loja e snack bar)
   ROTAS: /api/categorias
   ============================================== */

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoriaController extends Controller
{
    /**
     * ==============================================
     * MÉTODO: INDEX - LISTAR CATEGORIAS
     * ROTA: GET /api/categorias
     * DESCRIÇÃO: Retorna lista de categorias ativas com filtros opcionais
     * ==============================================
     */
    public function index(Request $request): JsonResponse
    {
        // Inicia query com scope para categorias ativas
        $query = Categoria::ativas();

        // FILTRO OPCIONAL: Por tipo de categoria (loja ou snack)
        if ($request->has('tipo')) {
            $query->porTipo($request->tipo);
        }

        // Carrega contagem de produtos relacionados e ordena por nome
        $categorias = $query->withCount('produtos')->orderBy('nome')->get();

        return response()->json($categorias);
    }

    /**
     * ==============================================
     * MÉTODO: STORE - CRIAR NOVA CATEGORIA
     * ROTA: POST /api/categorias
     * DESCRIÇÃO: Cria uma nova categoria após validação
     * ==============================================
     */
    public function store(Request $request): JsonResponse
    {
        // VALIDAÇÃO DOS DADOS DE ENTRADA
        $validated = $request->validate([
            'nome' => 'required|string|max:255',           // Nome obrigatório, máx 255 chars
            'descricao' => 'nullable|string',              // Descrição opcional
            'tipo' => 'required|in:loja,snack',            // Tipo obrigatório: "loja" ou "snack"
            'ativo' => 'boolean'                           // Status ativo (true/false)
        ]);

        // Cria categoria no banco de dados
        $categoria = Categoria::create($validated);

        // Retorna categoria criada com status HTTP 201 (Created)
        return response()->json($categoria, 201);
    }

    /**
     * ==============================================
     * MÉTODO: SHOW - EXIBIR CATEGORIA ESPECÍFICA
     * ROTA: GET /api/categorias/{id}
     * DESCRIÇÃO: Retorna dados de uma categoria incluindo contagem de produtos
     * ==============================================
     */
    public function show(Categoria $categoria): JsonResponse
    {
        // Carrega contagem de produtos relacionados
        $categoria->loadCount('produtos');
        return response()->json($categoria);
    }

    /**
     * ==============================================
     * MÉTODO: UPDATE - ATUALIZAR CATEGORIA
     * ROTA: PUT/PATCH /api/categorias/{id}
     * DESCRIÇÃO: Atualiza dados de uma categoria existente
     * ==============================================
     */
    public function update(Request $request, Categoria $categoria): JsonResponse
    {
        // VALIDAÇÃO DOS DADOS (usando 'sometimes' para campos opcionais na atualização)
        $validated = $request->validate([
            'nome' => 'sometimes|required|string|max:255', // Nome obrigatório se enviado
            'descricao' => 'nullable|string',              // Descrição opcional
            'tipo' => 'sometimes|required|in:loja,snack',  // Tipo obrigatório se enviado
            'ativo' => 'boolean'                           // Status ativo
        ]);

        // Atualiza categoria no banco de dados
        $categoria->update($validated);

        return response()->json($categoria);
    }

    /**
     * ==============================================
     * MÉTODO: DESTROY - EXCLUIR CATEGORIA
     * ROTA: DELETE /api/categorias/{id}
     * DESCRIÇÃO: Remove categoria do banco de dados
     * ==============================================
     */
    public function destroy(Categoria $categoria): JsonResponse
    {
        // Remove categoria (soft delete se configurado no model)
        $categoria->delete();
        
        return response()->json(['message' => 'Categoria excluída com sucesso']);
    }
}
