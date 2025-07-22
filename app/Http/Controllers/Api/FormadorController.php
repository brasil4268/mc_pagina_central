<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formador;
use Illuminate\Http\Request;

class FormadorController extends Controller
{
    // Listar todos os formadores com cursos e centros
    public function index()
    {
        $formadores = Formador::with(['cursos', 'centros'])->get();
        return response()->json(['status' => 'sucesso', 'dados' => $formadores]);
    }

    // Criar formador e associar cursos/centros
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'nullable|email|unique:formadores,email',
            'contactos' => ['required', 'array', 'min:1'],
            'contactos.*' => ['required', 'string', 'regex:/^9\d{8}$/'],
            'especialidade' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'foto_url' => 'nullable|url'
        ]);

        $formador = Formador::create($validated);

        // Associar cursos (opcional)
        if ($request->has('cursos')) {
            $formador->cursos()->sync($request->cursos);
        }
        // Associar centros (opcional)
        if ($request->has('centros')) {
            $formador->centros()->sync($request->centros);
        }

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Formador cadastrado com sucesso!',
            'dados' => $formador->load(['cursos', 'centros'])
        ], 201);
    }

    // Consultar formador por ID com cursos e centros
    public function show($id)
    {
        $formador = Formador::with(['cursos', 'centros'])->find($id);

        if (!$formador) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Formador não encontrado!'
            ], 404);
        }

        return response()->json(['status' => 'sucesso', 'dados' => $formador]);
    }

    // Atualizar formador e relacionamentos
    public function update(Request $request, $id)
    {
        $formador = Formador::find($id);

        if (!$formador) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Formador não encontrado!'
            ], 404);
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'nullable|email|unique:formadores,email,' . $id,
            'contactos' => ['required', 'array', 'min:1'],
            'contactos.*' => ['required', 'string', 'regex:/^9\d{8}$/'],
            'especialidade' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'foto_url' => 'nullable|url'
        ]);

        $formador->update($validated);

        // Atualizar cursos associados (opcional)
        if ($request->has('cursos')) {
            $formador->cursos()->sync($request->cursos);
        }
        // Atualizar centros associados (opcional)
        if ($request->has('centros')) {
            $formador->centros()->sync($request->centros);
        }

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Formador atualizado com sucesso!',
            'dados' => $formador->load(['cursos', 'centros'])
        ]);
    }

    // Remover formador
    public function destroy($id)
    {
        $formador = Formador::find($id);

        if (!$formador) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Formador não encontrado!'
            ], 404);
        }

        // Remove associações N:N antes de deletar
        $formador->cursos()->detach();
        $formador->centros()->detach();
        $formador->delete();

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Formador deletado com sucesso!'
        ]);
    }
}