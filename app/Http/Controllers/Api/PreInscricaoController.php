<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PreInscricao;
use Illuminate\Http\Request;

class PreInscricaoController extends Controller
{
    //
     // Usuário faz pré-inscrição
    public function store(Request $request)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'centro_id' => 'required|exists:centros,id',
            'horario_id' => 'nullable|exists:horarios,id',
            'nome_completo' => 'required|string|max:100',

            'contactos' => [
                'required',
                'array',
                'min:1'
            ],
            'contactos.*' => [
                'required',
                'string',
                'regex:/^9\d{8}$/'
            ],
            'email' => 'nullable|email|max:100',
            'observacoes' => 'nullable|string'
        ]);

        $preInscricao = PreInscricao::create($validated);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Pré-inscrição realizada!',
            'dados' => $preInscricao
        ], 201);
    }

    // Admin visualiza todas as pré-inscrições
    public function index()
    {
        $preInscricoes = PreInscricao::with(['curso', 'centro', 'horario'])->get();
        return response()->json(['status' => 'sucesso', 'dados' => $preInscricoes]);
    }

    // Admin atualiza status
    public function update(Request $request, $id)
    {
        $preInscricao = PreInscricao::find($id);

        if (!$preInscricao) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Pré-inscrição não encontrada!'
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:pendente,confirmado,cancelado',
            'observacoes' => 'nullable|string'
        ]);

        $preInscricao->update($validated);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Status atualizado!',
            'dados' => $preInscricao
        ]);
    }

    public function destroy($id)
    {
        $preInscricao = PreInscricao::find($id);

        if (!$preInscricao) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Pré-inscrição não encontrada!'
            ], 404);
        }

        $preInscricao->delete();

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Pré-inscrição deletada com sucesso!'
        ]);
    }

    public function show($id)
    {
        $preInscricao = PreInscricao::with(['curso', 'centro', 'horario'])->find($id);

        if (!$preInscricao) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Pré-inscrição não encontrada!'
            ], 404);
        }

        return response()->json([
            'status' => 'sucesso',
            'dados' => $preInscricao
        ]);
    }
}
