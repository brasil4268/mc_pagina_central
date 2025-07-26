<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PreInscricao;
use Illuminate\Http\Request;

class PreInscricaoController extends Controller
{
    //

    /**
 * @OA\Post(
 *     path="/api/pre-inscricoes",
 *     summary="Realizar pré-inscrição",
 *     tags={"Pré-inscrições"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"curso_id","centro_id","nome_completo","contactos"},
 *             @OA\Property(property="curso_id", type="integer", example=1),
 *             @OA\Property(property="centro_id", type="integer", example=1),
 *             @OA\Property(property="horario_id", type="integer", example=1),
 *             @OA\Property(property="nome_completo", type="string", example="João Pedro"),
 *             @OA\Property(property="contactos", type="array", @OA\Items(type="string", example="923111111")),
 *             @OA\Property(property="email", type="string", example="joao@teste.com"),
 *             @OA\Property(property="observacoes", type="string", example="Quero estudar à tarde.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Pré-inscrição realizada"
 *     )
 * )
 */
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
            'observacoes' => 'nullable|string|max:500'
        ]);

        // Formatar dados
        $validated['contactos'] = array_map('strval', $validated['contactos']);
        $validated['status'] = 'pendente'; // Status padrão
        
        // Normalizar email para lowercase se fornecido
        if (!empty($validated['email'])) {
            $validated['email'] = strtolower($validated['email']);
        }

        $preInscricao = PreInscricao::create($validated);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Pré-inscrição realizada!',
            'dados' => $preInscricao
        ], 201);
    }


    /**
 * @OA\Get(
 *     path="/api/pre-inscricoes",
 *     summary="Listar todas as pré-inscrições",
 *     tags={"Pré-inscrições"},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de pré-inscrições"
 *     )
 * )
 */
    // Admin visualiza todas as pré-inscrições
    public function index()
    {
        $preInscricoes = PreInscricao::with(['curso', 'centro', 'horario'])->get();
        return response()->json(['status' => 'sucesso', 'dados' => $preInscricoes]);
    }


    /**
 * @OA\Put(
 *     path="/api/pre-inscricoes/{id}",
 *     summary="Atualizar status da pré-inscrição",
 *     tags={"Pré-inscrições"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", enum={"pendente","confirmado","cancelado"}, example="pendente"),
 *             @OA\Property(property="observacoes", type="string", example="Observação atualizada")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status atualizado"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Pré-inscrição não encontrada"
 *     )
 * )
 */
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
            'curso_id' => 'required|exists:cursos,id',
            'centro_id' => 'required|exists:centros,id',
            'horario_id' => 'nullable|exists:horarios,id',
            'nome_completo' => 'required|string|max:100',
            'contactos' => ['required', 'array', 'min:1'],
            'contactos.*' => ['required', 'string', 'regex:/^9\d{8}$/'],
            'email' => 'nullable|email|max:100',
            'observacoes' => 'nullable|string|max:500'
        ]);
        $validated = $request->validate([
            'status' => 'required|in:pendente,confirmado,cancelado',
            'observacoes' => 'nullable|string|max:500'
        ]);

        $preInscricao->update($validated);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Status atualizado!',
            'dados' => $preInscricao
        ]);
    }


    /**
 * @OA\Delete(
 *     path="/api/pre-inscricoes/{id}",
 *     summary="Deletar pré-inscrição",
 *     tags={"Pré-inscrições"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Pré-inscrição deletada com sucesso"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Pré-inscrição não encontrada"
 *     )
 * )
 */
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


    /**
 * @OA\Get(
 *     path="/api/pre-inscricoes/{id}",
 *     summary="Buscar pré-inscrição por ID",
 *     tags={"Pré-inscrições"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Pré-inscrição encontrada"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Pré-inscrição não encontrada"
 *     )
 * )
 */
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
