<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



use App\Http\Controllers\Api\CentroController;

//Rota para pegar todos
Route::get('/centros', [CentroController::class, 'index']);
//Rota para criar
Route::post('/centros', [CentroController::class, 'store']);
//Rota para pegar por ID
Route::get('/centros/{id}', [CentroController::class, 'show']);
//Rota para update
Route::put('/centros/{id}', [CentroController::class, 'update']);
//Rota para o delete
Route::delete('/centros/{id}', [CentroController::class, 'destroy']);


//ROTAS PARA CURSOS
use App\Http\Controllers\Api\CursoController;
//Rota para pegar todos os cursos
Route::get('/cursos', [CursoController::class, 'index']);
Route::post('/cursos', [CursoController::class, 'store']);
Route::get('/cursos/{id}', [CursoController::class, 'show']);
Route::put('/cursos/{id}', [CursoController::class, 'update']);
Route::delete('/cursos/{id}', [CursoController::class, 'destroy']);

//ROTAS PARA HORÁRIOS
use App\Http\Controllers\Api\HorarioController;

Route::get('/horarios', [HorarioController::class, 'index']);
Route::post('/horarios', [HorarioController::class, 'store']);
Route::get('/horarios/{id}', [HorarioController::class, 'show']);
Route::put('/horarios/{id}', [HorarioController::class, 'update']);
Route::delete('/horarios/{id}', [HorarioController::class, 'destroy']);

//ROTAS PARA FORMADORES
use App\Http\Controllers\Api\FormadorController;

Route::get('/formadores', [FormadorController::class, 'index']);
Route::post('/formadores', [FormadorController::class, 'store']);
Route::get('/formadores/{id}', [FormadorController::class, 'show']);
Route::put('/formadores/{id}', [FormadorController::class, 'update']);
Route::delete('/formadores/{id}', [FormadorController::class, 'destroy']);


//ROTAS PARA PRE_INSCRIÇÕES
use App\Http\Controllers\Api\PreInscricaoController;

Route::post('/pre-inscricoes', [PreInscricaoController::class, 'store']); // Usuário
Route::get('/pre-inscricoes', [PreInscricaoController::class, 'index']); // Admin
Route::put('/pre-inscricoes/{id}', [PreInscricaoController::class, 'update']); // Admin
Route::get('/pre-inscricoes/{id}', [PreInscricaoController::class, 'show']);
Route::delete('/pre-inscricoes/{id}', [PreInscricaoController::class, 'destroy']);

//ROTAS PARA CATEGORIAS
use App\Http\Controllers\CategoriaController;

Route::get('/categorias', [CategoriaController::class, 'index']);
Route::post('/categorias', [CategoriaController::class, 'store']);
Route::get('/categorias/{categoria}', [CategoriaController::class, 'show']);
Route::put('/categorias/{categoria}', [CategoriaController::class, 'update']);
Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy']);

//ROTAS PARA PRODUTOS
use App\Http\Controllers\ProdutoController;

Route::get('/produtos', [ProdutoController::class, 'index']);
Route::post('/produtos', [ProdutoController::class, 'store']);
Route::get('/produtos/{produto}', [ProdutoController::class, 'show']);
Route::put('/produtos/{produto}', [ProdutoController::class, 'update']);
Route::delete('/produtos/{produto}', [ProdutoController::class, 'destroy']);

// Rotas específicas para a página pública
Route::get('/produtos/em-destaque', [ProdutoController::class, 'emDestaque']);
Route::get('/categorias/{categoria}/produtos', [ProdutoController::class, 'porCategoria']);