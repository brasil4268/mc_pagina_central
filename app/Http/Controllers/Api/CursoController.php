<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    //

    // Listar todos os cursos
    public function index()
    {
        return response()->json([
            'status' => 'sucesso',
            'dados' => Curso::with('centro')->get()
        ]);
    }

    // Criar curso
    public function store(Request $request)
    {
        $validated = $request->validate([
            'centro_id' => 'required|exists:centros,id',
            'nome' => 'required|string|max:100',
            'descricao' => 'required|string',
            'programa' => 'required|string',
            'duracao' => 'required|string|max:50',
            'preco' => 'required|numeric|min:0',
            'area' => 'required|string|max:100',
            'modalidade' => 'required|in:presencial,online',
            'imagem_url' => 'nullable|url',
            'ativo' => 'boolean'
        ]);

        $curso = Curso::create($validated);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Curso cadastrado com sucesso!',
            'dados' => $curso
        ], 201);
    }

    // Buscar curso por ID
    public function show($id)
    {
        $curso = Curso::with('centro')->find($id);

        if (!$curso) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Curso não encontrado!'
            ], 404);
        }

        return response()->json([
            'status' => 'sucesso',
            'dados' => $curso
        ]);
    }

    // Atualizar curso
    public function update(Request $request, $id)
    {
        $curso = Curso::find($id);

        if (!$curso) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Curso não encontrado!'
            ], 404);
        }

        $validated = $request->validate([
            'centro_id' => 'required|exists:centros,id',
            'nome' => 'required|string|max:100',
            'descricao' => 'required|string',
            'programa' => 'required|string',
            'duracao' => 'required|string|max:50',
            'preco' => 'required|numeric|min:0',
            'area' => 'required|string|max:100',
            'modalidade' => 'required|in:presencial,online',
            'imagem_url' => 'nullable|url',
            'ativo' => 'boolean'
        ]);

        $curso->update($validated);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Curso atualizado com sucesso!',
            'dados' => $curso
        ]);
    }

    // Deletar curso
    public function destroy($id)
    {
        $curso = Curso::find($id);

        if (!$curso) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Curso não encontrado!'
            ], 404);
        }

        $curso->delete();

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Curso deletado com sucesso!'
        ]);
    }
}
