<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Produto::with('categoria');

        // Filtrar por categoria se especificado
        if ($request->has('categoria_id')) {
            $query->porCategoria($request->categoria_id);
        }

        // Filtrar por tipo (loja ou snack)
        if ($request->has('tipo')) {
            $query->whereHas('categoria', function($q) use ($request) {
                $q->porTipo($request->tipo);
            });
        }

        // Filtrar apenas em destaque
        if ($request->has('em_destaque') && $request->em_destaque) {
            $query->emDestaque();
        }

        // Filtrar apenas ativos se não for admin
        if (!$request->has('incluir_inativos')) {
            $query->ativos();
        }

        $produtos = $query->orderBy('created_at', 'desc')->get();

        return response()->json($produtos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'imagem' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'ativo' => 'boolean',
            'em_destaque' => 'boolean'
        ]);

        $produto = Produto::create($validated);
        $produto->load('categoria');

        return response()->json($produto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto): JsonResponse
    {
        $produto->load('categoria');
        return response()->json($produto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produto $produto): JsonResponse
    {
        $validated = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'sometimes|required|numeric|min:0',
            'imagem' => 'nullable|string',
            'categoria_id' => 'sometimes|required|exists:categorias,id',
            'ativo' => 'boolean',
            'em_destaque' => 'boolean'
        ]);

        $produto->update($validated);
        $produto->load('categoria');

        return response()->json($produto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto): JsonResponse
    {
        $produto->delete();
        return response()->json(['message' => 'Produto excluído com sucesso']);
    }

    /**
     * Get produtos por categoria para a página pública
     */
    public function porCategoria(Categoria $categoria): JsonResponse
    {
        $produtos = $categoria->produtos()->ativos()->get();
        return response()->json($produtos);
    }

    /**
     * Get produtos em destaque
     */
    public function emDestaque(Request $request): JsonResponse
    {
        $query = Produto::with('categoria')->ativos()->emDestaque();

        if ($request->has('tipo')) {
            $query->whereHas('categoria', function($q) use ($request) {
                $q->porTipo($request->tipo);
            });
        }

        $produtos = $query->get();
        return response()->json($produtos);
    }
}
