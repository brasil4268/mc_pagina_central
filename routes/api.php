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
