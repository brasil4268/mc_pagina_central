<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Centro;
use Illuminate\Http\Request;

class CentroController extends Controller
{
    //
    public function index()
    {
        //retorna todos os centros
        return response()->json([
            'status' => 'sucesso',
            'dados' => Centro::all()
        ]);
    }

    //Criar
    public function store(Request $request)
    {
        //Validação dos dados recebidos
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

        // Inserção dos dados validados no banco
        $centro = Centro::create($validated);

        // Retorno de resposta JSON para o frontend
        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Centro cadastrado com sucesso!',
            'dados' => $centro
        ], 201); // 201 = Created
    }


    //Busca por ID
    public function show($id)
    {
        // Busca o centro pelo ID
        $centro = Centro::find($id);

        // Verifica se o centro foi encontrado
        if (!$centro) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Centro não encontrado!'
            ], 404); // 404 = Not Found
        }

        // Retorna o centro encontrado
        return response()->json([
            'status' => 'sucesso',
            'dados' => $centro
        ]);
    }

    //Editar
    public function update(Request $request, $id)
    {
        $centro = Centro::find($id);

        if (!$centro)
        {
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

        // Atualização dos dados do centro
        $centro->update($validated);

        // Retorno de resposta JSON para os wi do frontend
        return response()->json([
            'status' => 'sucesso',
            'mensagem' => 'Centro atualizado com sucesso!',
            'dados' => $centro
        ]);
    }

    //Deletar
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
