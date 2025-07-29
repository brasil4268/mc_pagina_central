<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\Curso;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    //
    /**
 * @OA\Get(
 *     path="/api/horarios",
 *     summary="Listar todos os horários",
 *     tags={"Horários"},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de horários"
 *     )
 * )
 */
     public function index()
    {
        $horarios = Horario::with('curso')->get();
        return response()->json($horarios);
    }



/**
 * @OA\Post(
 *     path="/api/horarios",
 *     summary="Criar um novo horário",
 *     tags={"Horários"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"curso_id","dia_semana","periodo","hora_inicio","hora_fim"},
 *             @OA\Property(property="curso_id", type="integer", example=1),
 *             @OA\Property(property="dia_semana", type="string", example="Segunda"),
 *             @OA\Property(property="periodo", type="string", example="manhã"),
 *             @OA\Property(property="hora_inicio", type="string", example="08:00"),
 *             @OA\Property(property="hora_fim", type="string", example="10:00")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Horário cadastrado com sucesso"
 *     )
 * )
 */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'centro_id' => 'required|exists:centros,id',
            'dia_semana' => 'required|in:Segunda,Terça,Quarta,Quinta,Sexta,Sábado,Domingo',
            'periodo' => 'required|in:manhã,tarde,noite',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio'
        ]);

        // Garantir formato correto das horas
        $validated['hora_inicio'] = date('H:i', strtotime($validated['hora_inicio']));
        $validated['hora_fim'] = date('H:i', strtotime($validated['hora_fim']));

        // Verificar conflitos de horário
        $conflitos = $this->verificarConflitosHorario($validated);
        if (!empty($conflitos)) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Conflito de horário detectado!',
                'conflitos' => $conflitos
            ], 422);
        }

        $horario = Horario::create($validated);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Horário cadastrado com sucesso!',
            'dados' => $horario
        ], 201);
    }


    /**
 * @OA\Get(
 *     path="/api/horarios/{id}",
 *     summary="Buscar horário por ID",
 *     tags={"Horários"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Horário encontrado"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Horário não encontrado"
 *     )
 * )
 */
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


    /**
 * @OA\Put(
 *     path="/api/horarios/{id}",
 *     summary="Atualizar horário",
 *     tags={"Horários"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"curso_id","dia_semana","periodo","hora_inicio","hora_fim"},
 *             @OA\Property(property="curso_id", type="integer", example=1),
 *             @OA\Property(property="dia_semana", type="string", example="Segunda"),
 *             @OA\Property(property="periodo", type="string", example="manhã"),
 *             @OA\Property(property="hora_inicio", type="string", example="08:00"),
 *             @OA\Property(property="hora_fim", type="string", example="10:00")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Horário atualizado com sucesso"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Horário não encontrado"
 *     )
 * )
 */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'centro_id' => 'required|exists:centros,id',
            'dia_semana' => 'required|in:Segunda,Terça,Quarta,Quinta,Sexta,Sábado,Domingo',
            'periodo' => 'required|in:manhã,tarde,noite',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio'
        ]);
        
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
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio'
        ]);

        // Garantir formato correto das horas
        $validated['hora_inicio'] = date('H:i', strtotime($validated['hora_inicio']));
        $validated['hora_fim'] = date('H:i', strtotime($validated['hora_fim']));

        // Verificar conflitos de horário (ignorando o horário atual)
        $conflitos = $this->verificarConflitosHorario($validated, $id);
        if (!empty($conflitos)) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Conflito de horário detectado!',
                'conflitos' => $conflitos
            ], 422);
        }

        $horario->update($validated);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Horário atualizado com sucesso!',
            'dados' => $horario
        ]);
    }


    /**
 * @OA\Delete(
 *     path="/api/horarios/{id}",
 *     summary="Deletar horário",
 *     tags={"Horários"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Horário deletado com sucesso"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Horário não encontrado"
 *     )
 * )
 */
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

    /**
     * Verificar conflitos de horário para um mesmo formador
     */
    private function verificarConflitosHorario($dadosHorario, $horarioIdIgnorar = null)
    {
        $conflitos = [];
        
        // Buscar o curso e seus formadores
        $curso = Curso::with('formadores')->find($dadosHorario['curso_id']);
        
        if (!$curso || $curso->formadores->isEmpty()) {
            return $conflitos; // Sem formadores, sem conflitos
        }

        foreach ($curso->formadores as $formador) {
            // Buscar todos os horários dos cursos deste formador
            $horariosFormador = Horario::whereHas('curso.formadores', function($query) use ($formador) {
                $query->where('formadores.id', $formador->id);
            })
            ->where('dia_semana', $dadosHorario['dia_semana'])
            ->when($horarioIdIgnorar, function($query, $id) {
                return $query->where('id', '!=', $id);
            })
            ->with(['curso.formadores'])
            ->get();

            foreach ($horariosFormador as $horarioExistente) {
                if ($this->horariosSeConflitam(
                    $dadosHorario['hora_inicio'], 
                    $dadosHorario['hora_fim'],
                    $horarioExistente->hora_inicio, 
                    $horarioExistente->hora_fim
                )) {
                    $conflitos[] = [
                        'formador' => $formador->nome,
                        'curso_conflitante' => $horarioExistente->curso->nome,
                        'dia_semana' => $horarioExistente->dia_semana,
                        'periodo' => $horarioExistente->periodo,
                        'hora_inicio' => $horarioExistente->hora_inicio,
                        'hora_fim' => $horarioExistente->hora_fim,
                        'mensagem' => "Formador {$formador->nome} já tem aula do curso '{$horarioExistente->curso->nome}' das {$horarioExistente->hora_inicio} às {$horarioExistente->hora_fim}"
                    ];
                }
            }
        }

        return $conflitos;
    }

    /**
     * Verificar se dois horários se conflitam
     */
    private function horariosSeConflitam($inicio1, $fim1, $inicio2, $fim2)
    {
        // Converter para timestamps para facilitar comparação
        $inicio1 = strtotime($inicio1);
        $fim1 = strtotime($fim1);
        $inicio2 = strtotime($inicio2);
        $fim2 = strtotime($fim2);

        // Verificar se há sobreposição
        return ($inicio1 < $fim2) && ($fim1 > $inicio2);
    }
}

