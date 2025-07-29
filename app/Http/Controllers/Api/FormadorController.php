<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formador;
use Illuminate\Http\Request;

class FormadorController extends Controller
{

    /**
 * @OA\Get(
 *     path="/api/formadores",
 *     summary="Listar todos os formadores",
 *     tags={"Formadores"},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de formadores"
 *     )
 * )
 */
    // Listar todos os formadores com cursos e centros
    public function index()
    {
        $formadores = Formador::with(['cursos', 'centros'])->get();
        return response()->json($formadores);
    }


    /**
 * @OA\Post(
 *     path="/api/formadores",
 *     summary="Criar um novo formador",
 *     tags={"Formadores"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nome","contactos"},
 *             @OA\Property(property="nome", type="string", example="Ana Silva"),
 *             @OA\Property(property="email", type="string", example="ana@formador.com"),
 *             @OA\Property(property="contactos", type="array", @OA\Items(type="string", example="923888888")),
 *             @OA\Property(property="especialidade", type="string", example="Informática"),
 *             @OA\Property(property="bio", type="string", example="Especialista em informática."),
 *             @OA\Property(property="foto_url", type="string", example="https://exemplo.com/foto.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Formador cadastrado com sucesso"
 *     )
 * )
 */
    // Criar formador e associar cursos/centros
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'nullable|email|max:100|unique:formadores,email',
            'contactos' => ['required', 'array', 'min:1'],
            'contactos.*' => ['required', 'string', 'regex:/^9\d{8}$/'],
            'especialidade' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'foto_url' => 'nullable|url|max:255'
        ]);

        // Formatar contactos para garantir que são strings
        $validated['contactos'] = array_map('strval', $validated['contactos']);
        
        // Normalizar email para lowercase se fornecido
        if (!empty($validated['email'])) {
            $validated['email'] = strtolower($validated['email']);
        }

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


    /**
 * @OA\Get(
 *     path="/api/formadores/{id}",
 *     summary="Buscar formador por ID",
 *     tags={"Formadores"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Formador encontrado"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Formador não encontrado"
 *     )
 * )
 */
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


    /**
 * @OA\Put(
 *     path="/api/formadores/{id}",
 *     summary="Atualizar formador",
 *     tags={"Formadores"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nome","contactos"},
 *             @OA\Property(property="nome", type="string", example="Ana Silva"),
 *             @OA\Property(property="email", type="string", example="ana@formador.com"),
 *             @OA\Property(property="contactos", type="array", @OA\Items(type="string", example="923888888")),
 *             @OA\Property(property="especialidade", type="string", example="Informática"),
 *             @OA\Property(property="bio", type="string", example="Especialista em informática."),
 *             @OA\Property(property="foto_url", type="string", example="https://exemplo.com/foto.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Formador atualizado com sucesso"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Formador não encontrado"
 *     )
 * )
 */
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
            'email' => 'nullable|email|max:100|unique:formadores,email' . ($request->method() === 'PUT' ? ',' . $id : ''),
            'contactos' => ['required', 'array', 'min:1'],
            'contactos.*' => ['required', 'string', 'regex:/^9\d{8}$/'],
            'especialidade' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'foto_url' => 'nullable|url|max:255',
            'cursos' => 'array',
            'cursos.*' => 'exists:cursos,id',
            'centros' => 'array',
            'centros.*' => 'exists:centros,id'
        ]);

        // Formatar contactos para garantir que são strings
        $validated['contactos'] = array_map('strval', $validated['contactos']);
        
        // Normalizar email para lowercase se fornecido
        if (!empty($validated['email'])) {
            $validated['email'] = strtolower($validated['email']);
        }

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


    /**
 * @OA\Delete(
 *     path="/api/formadores/{id}",
 *     summary="Deletar formador",
 *     tags={"Formadores"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Formador deletado com sucesso"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Formador não encontrado"
 *     )
 * )
 */
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