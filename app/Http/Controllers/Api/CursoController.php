<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cursos",
     *     summary="Listar todos os cursos",
     *     tags={"Cursos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de cursos"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Curso::with(['centros', 'formadores']);

        // Filtro por múltiplos centros (busca em relacionamento many-to-many)
        if ($request->filled('centros')) {
            $centros = is_array($request->centros) ? $request->centros : explode(',', $request->centros);
            $query->whereHas('centros', function($q) use ($centros) {
                $q->whereIn('centros.id', $centros);
            });
        }

        // Filtro por área
        if ($request->filled('area')) {
            $query->where('area', 'like', '%' . $request->area . '%');
        }

        // Filtro por modalidade
        if ($request->filled('modalidade')) {
            $query->where('modalidade', $request->modalidade);
        }

        // Filtro por status (ativo/inativo)
        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        // Filtro por faixa de preço (na tabela pivô centro_curso)
        if ($request->filled('preco_min') || $request->filled('preco_max')) {
            $query->whereHas('centros', function($q) use ($request) {
                if ($request->filled('preco_min')) {
                    $q->wherePivot('preco', '>=', $request->preco_min);
                }
                if ($request->filled('preco_max')) {
                    $q->wherePivot('preco', '<=', $request->preco_max);
                }
            });
        }

        // Filtro por data de arranque (na tabela pivô centro_curso)
        if ($request->filled('data_arranque')) {
            $query->whereHas('centros', function($q) use ($request) {
                $q->wherePivot('data_arranque', $request->data_arranque);
            });
        }

        // Filtro por data de criação
        if ($request->filled('data_inicio')) {
            $query->where('created_at', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->where('created_at', '<=', $request->data_fim . ' 23:59:59');
        }

        // Busca textual em múltiplos campos
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function($q) use ($busca) {
                $q->where('nome', 'like', '%' . $busca . '%')
                  ->orWhere('descricao', 'like', '%' . $busca . '%')
                  ->orWhere('programa', 'like', '%' . $busca . '%')
                  ->orWhere('area', 'like', '%' . $busca . '%')
                  ->orWhereHas('centros', function($subQ) use ($busca) {
                      $subQ->where('nome', 'like', '%' . $busca . '%')
                           ->orWhere('localizacao', 'like', '%' . $busca . '%');
                  })
                  ->orWhereHas('formadores', function($subQ) use ($busca) {
                      $subQ->where('nome', 'like', '%' . $busca . '%')
                           ->orWhere('especialidade', 'like', '%' . $busca . '%');
                  });
            });
        }

        // Ordenação
        $ordenarPor = $request->get('ordenar_por', 'created_at');
        $ordenarDirecao = $request->get('ordenar_direcao', 'desc');
        $query->orderBy($ordenarPor, $ordenarDirecao);

        // Paginação
        if ($request->filled('paginar') && $request->paginar == 'true') {
            $porPagina = $request->get('por_pagina', 10);
            $cursos = $query->paginate($porPagina);
        } else {
            $cursos = $query->get();
        }

        return response()->json(['status' => 'sucesso', 'dados' => $cursos]);
    }

    /**
     * @OA\Post(
     *     path="/api/cursos",
     *     summary="Criar um novo curso",
     *     tags={"Cursos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome","descricao","programa","area","modalidade","centros"},
     *             @OA\Property(property="nome", type="string", example="Informática Básica"),
     *             @OA\Property(property="descricao", type="string", example="Curso de introdução à informática."),
     *             @OA\Property(property="programa", type="string", example="Windows, Word, Excel"),
     *             @OA\Property(property="area", type="string", example="Tecnologia"),
     *             @OA\Property(property="modalidade", type="string", enum={"presencial","online"}, example="presencial"),
     *             @OA\Property(property="imagem_url", type="string", example="https://exemplo.com/imagem.jpg"),
     *             @OA\Property(property="ativo", type="boolean", example=true),
     *             @OA\Property(
     *                 property="centros",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"centro_id","preco","duracao"},
     *                     @OA\Property(property="centro_id", type="integer", example=1),
     *                     @OA\Property(property="preco", type="number", example=10000),
     *                     @OA\Property(property="duracao", type="string", example="2 meses"),
     *                     @OA\Property(property="data_arranque", type="string", format="date", example="2025-08-01")
     *                 )
     *             ),
     *             @OA\Property(property="formadores", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Curso cadastrado com sucesso"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'required|string',
            'programa' => 'required|string',
            'area' => 'required|string|max:100',
            'modalidade' => 'required|in:presencial,online',
            'imagem_url' => 'nullable|url|max:255',
            'ativo' => 'boolean',
            'centros' => 'required|array|min:1',
            'centros.*.centro_id' => 'required|exists:centros,id',
            'centros.*.preco' => 'required|numeric|min:0',
            'centros.*.duracao' => 'required|string|max:50',
            'centros.*.data_arranque' => 'nullable|date',
            'formadores' => 'array'
        ]);

        // Separe apenas os campos do curso
        $cursoData = collect($validated)->only([
            'nome',
            'descricao',
            'programa',
            'area',
            'modalidade',
            'imagem_url',
            'ativo'
        ])->toArray();

        $curso = Curso::create($cursoData);

        // Associar centros com dados extras (preço, duração e data_arranque)
        $centrosPivot = [];
        foreach ($validated['centros'] as $centro) {
            $centrosPivot[$centro['centro_id']] = [
                'preco' => $centro['preco'],
                'duracao' => $centro['duracao'],
                'data_arranque' => $centro['data_arranque'] ?? null,
            ];
        }
        $curso->centros()->sync($centrosPivot);

        // Sincronizar formadores
        if (!empty($validated['formadores'])) {
            $curso->formadores()->sync($validated['formadores']);
        }

        $curso->load(['centros', 'formadores']);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Curso cadastrado com sucesso!',
            'dados' => $curso
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/cursos/{id}",
     *     summary="Buscar curso por ID",
     *     tags={"Cursos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Curso encontrado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Curso não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $curso = Curso::with(['centros', 'formadores'])->find($id);

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

    /**
     * @OA\Put(
     *     path="/api/cursos/{id}",
     *     summary="Atualizar curso",
     *     tags={"Cursos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome","descricao","programa","area","modalidade","centros"},
     *             @OA\Property(property="nome", type="string", example="Informática Básica"),
     *             @OA\Property(property="descricao", type="string", example="Curso de introdução à informática."),
     *             @OA\Property(property="programa", type="string", example="Windows, Word, Excel"),
     *             @OA\Property(property="area", type="string", example="Tecnologia"),
     *             @OA\Property(property="modalidade", type="string", enum={"presencial","online"}, example="presencial"),
     *             @OA\Property(property="imagem_url", type="string", example="https://exemplo.com/imagem.jpg"),
     *             @OA\Property(property="ativo", type="boolean", example=true),
     *             @OA\Property(
     *                 property="centros",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"centro_id","preco","duracao"},
     *                     @OA\Property(property="centro_id", type="integer", example=1),
     *                     @OA\Property(property="preco", type="number", example=10000),
     *                     @OA\Property(property="duracao", type="string", example="2 meses"),
     *                     @OA\Property(property="data_arranque", type="string", format="date", example="2025-08-01")
     *                 )
     *             ),
     *             @OA\Property(property="formadores", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Curso atualizado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Curso não encontrado"
     *     )
     * )
     */
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
            'nome' => 'required|string|max:100',
            'descricao' => 'required|string',
            'programa' => 'required|string',
            'area' => 'required|string|max:100',
            'modalidade' => 'required|in:presencial,online',
            'imagem_url' => 'nullable|url|max:255',
            'ativo' => 'boolean',
            'centros' => 'required|array|min:1',
            'centros.*.centro_id' => 'required|exists:centros,id',
            'centros.*.preco' => 'required|numeric|min:0',
            'centros.*.duracao' => 'required|string|max:50',
            'centros.*.data_arranque' => 'nullable|date',
            'formadores' => 'array'
        ]);

        // Separe apenas os campos do curso
        $cursoData = collect($validated)->only([
            'nome',
            'descricao',
            'programa',
            'area',
            'modalidade',
            'imagem_url',
            'ativo'
        ])->toArray();

        $curso->update($cursoData);

        // Atualizar associações com centros (preço, duração e data_arranque)
        $centrosPivot = [];
        foreach ($validated['centros'] as $centro) {
            $centrosPivot[$centro['centro_id']] = [
                'preco' => $centro['preco'],
                'duracao' => $centro['duracao'],
                'data_arranque' => $centro['data_arranque'] ?? null,
            ];
        }
        $curso->centros()->sync($centrosPivot);

        // Sincronizar formadores
        if (!empty($validated['formadores'])) {
            $curso->formadores()->sync($validated['formadores']);
        }

        $curso->load(['centros', 'formadores']);

        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Curso atualizado com sucesso!',
            'dados' => $curso
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/cursos/{id}",
     *     summary="Deletar curso",
     *     tags={"Cursos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Curso deletado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Curso não encontrado"
     *     )
     * )
     */
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
