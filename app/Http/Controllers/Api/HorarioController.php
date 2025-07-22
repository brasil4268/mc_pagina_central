<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    //
     public function index()
    {
        $horarios = Horario::with('curso')->get();
        return response()->json(['status' => 'sucesso', 'dados' => $horarios]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'dia_semana' => 'required|in:Segunda,Terça,Quarta,Quinta,Sexta,Sábado,Domingo',
            'periodo' => 'required|in:manhã,tarde,noite',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fim' => 'nullable|date_format:H:i'
        ]);

        $horario = Horario::create($validated);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Horário cadastrado com sucesso!',
            'dados' => $horario
        ], 201);
    }

    public function show($id)
    {
        $horario = Horario::with('curso')->find($id);

        if (!$horario) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Horário não encontrado!'
            ], 404);
        }

        return response()->json(['status' => 'sucesso', 'dados' => $horario]);
    }

    public function update(Request $request, $id)
    {
        $horario = Horario::find($id);

        if (!$horario) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Horário não encontrado!'
            ], 404);
        }

        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'dia_semana' => 'required|in:Segunda,Terça,Quarta,Quinta,Sexta,Sábado,Domingo',
            'periodo' => 'required|in:manhã,tarde,noite',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fim' => 'nullable|date_format:H:i'
        ]);

        $horario->update($validated);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Horário atualizado com sucesso!',
            'dados' => $horario
        ]);
    }

    public function destroy($id)
    {
        $horario = Horario::find($id);

        if (!$horario) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Horário não encontrado!'
            ], 404);
        }

        $horario->delete();

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Horário deletado com sucesso!'
        ]);
    }
}

