<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Centro;
use Illuminate\Http\Request;

class CentroController extends Controller
{
    //

    /**
 * @OA\Get(
 *     path="/api/centros",
 *     summary="Listar todos os centros",
 *     tags={"Centros"},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de centros"
 *     )
 * )
 */
    public function index()
    {
        // Retorna todos os centros diretamente como array
        return response()->json(Centro::all());
    }

/**
 * @OA\Post(
 *     path="/api/centros",
 *     summary="Criar um novo centro",
 *     tags={"Centros"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nome","localizacao","contactos"},
 *             @OA\Property(property="nome", type="string", example="Centro Alpha"),
 *             @OA\Property(property="localizacao", type="string", example="Luanda"),
 *             @OA\Property(property="contactos", type="array", @OA\Items(type="string", example="923111111")),
 *             @OA\Property(property="email", type="string", example="alpha@centro.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Centro cadastrado com sucesso"
 *     )
 * )
 */

    // Criar
    public function store(Request $request)
    {
        // Validação dos dados recebidos
        $validated = $request->validate([
            'nome' => [
                'required',
                'string',
                'max:100',
                'unique:centros,nome'
            ],
            'localizacao' => [
                'required',
                'string',
                'max:150'
            ],
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
            'email' => [
                'nullable',
                'email',
                'max:100'
            ]
        ]);

        // Formatar dados
        $validated['contactos'] = array_map('strval', $validated['contactos']);
        
        // Normalizar email para lowercase se fornecido
        if (!empty($validated['email'])) {
            $validated['email'] = strtolower($validated['email']);
        }

        // Inserção dos dados validados no banco
        $centro = Centro::create($validated);

        // Retorno de resposta JSON para o frontend
        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Centro cadastrado com sucesso!',
            'dados' => $centro
        ], 201); // 201 = Created
    }

    // Busca por ID com cursos e formadores associados

    /**
 * @OA\Get(
 *     path="/api/centros/{id}",
 *     summary="Buscar centro por ID",
 *     tags={"Centros"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Centro encontrado"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Centro não encontrado"
 *     )
 * )
 */
    public function show($id)
    {
        // Busca o centro pelo ID, incluindo cursos e formadores relacionados
        $centro = Centro::with(['cursos', 'formadores'])->find($id);

        // Verifica se o centro foi encontrado
        if (!$centro) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Centro não encontrado!'
            ], 404); // 404 = Not Found
        }

        // Retorna o centro encontrado com relacionamentos
        return response()->json([
            'status' => 'sucesso',
            'dados' => $centro
        ]);
    }

    // Editar

    /**
 * @OA\Put(
 *     path="/api/centros/{id}",
 *     summary="Atualizar centro",
 *     tags={"Centros"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nome","localizacao","contactos"},
 *             @OA\Property(property="nome", type="string", example="Centro Beta"),
 *             @OA\Property(property="localizacao", type="string", example="Benguela"),
 *             @OA\Property(property="contactos", type="array", @OA\Items(type="string", example="923222222")),
 *             @OA\Property(property="email", type="string", example="beta@centro.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Centro atualizado com sucesso"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Centro não encontrado"
 *     )
 * )
 */
    public function update(Request $request, $id)
    {
        $centro = Centro::find($id);

        if (!$centro) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Centro não encontrado!'
            ], 404); // 404 = Not Found
        }

        // Validação dos dados recebidos
        $validated = $request->validate([
            'nome' => [
                'required',
                'string',
                'max:100',
                'unique:centros,nome,' . $centro->id
            ],
            'localizacao' => [
                'required',
                'string',
                'max:150'
            ],
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
            'email' => [
                'nullable',
                'email',
                'max:100'
            ]
        ]);

        // Formatar dados
        $validated['contactos'] = array_map('strval', $validated['contactos']);
        
        // Normalizar email para lowercase se fornecido
        if (!empty($validated['email'])) {
            $validated['email'] = strtolower($validated['email']);
        }

        // Atualização dos dados do centro
        $centro->update($validated);

        // Retorno de resposta JSON para o frontend
        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Centro atualizado com sucesso!',
            'dados' => $centro
        ]);
    }

    // Deletar

    /**
 * @OA\Delete(
 *     path="/api/centros/{id}",
 *     summary="Deletar centro",
 *     tags={"Centros"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Centro deletado com sucesso"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Centro não encontrado"
 *     )
 * )
 */
    public function destroy($id)
    {
        $centro = Centro::find($id);

        if (!$centro) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Centro não encontrado!'
            ], 404); // 404 = Not Found
        }

        // Deletar o centro
        $centro->delete();

        // Retorno de resposta JSON para o frontend
        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Centro deletado com sucesso!'
        ]);
    }
}
