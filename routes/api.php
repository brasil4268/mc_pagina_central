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