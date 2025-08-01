<?php

/* ==============================================
   ARQUIVO: ROTAS API
   DESCRIÇÃO: Define todas as rotas da API REST
   PREFIXO: /api (definido no RouteServiceProvider)
   FORMATO: JSON responses
   ============================================== */

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

/* ==============================================
   ROTA: AUTENTICAÇÃO COM SANCTUM
   DESCRIÇÃO: Retorna dados do usuário autenticado
   ============================================== */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* ==============================================
   API: GESTÃO DE CENTROS DE FORMAÇÃO
   PREFIXO: /api/centros
   CONTROLLER: Api\CentroController
   ============================================== */
use App\Http\Controllers\Api\CentroController;

Route::get('/centros', [CentroController::class, 'index']);           // GET - Listar todos os centros
Route::post('/centros', [CentroController::class, 'store']);          // POST - Criar novo centro
Route::get('/centros/{id}', [CentroController::class, 'show']);       // GET - Exibir centro específico
Route::put('/centros/{id}', [CentroController::class, 'update']);     // PUT - Atualizar centro
Route::delete('/centros/{id}', [CentroController::class, 'destroy']); // DELETE - Excluir centro

/* ==============================================
   API: GESTÃO DE CURSOS
   PREFIXO: /api/cursos
   CONTROLLER: Api\CursoController
   ============================================== */
use App\Http\Controllers\Api\CursoController;

Route::get('/cursos', [CursoController::class, 'index']);             // GET - Listar todos os cursos
Route::post('/cursos', [CursoController::class, 'store']);            // POST - Criar novo curso
Route::get('/cursos/{id}', [CursoController::class, 'show']);         // GET - Exibir curso específico
Route::put('/cursos/{id}', [CursoController::class, 'update']);       // PUT - Atualizar curso
Route::delete('/cursos/{id}', [CursoController::class, 'destroy']);   // DELETE - Excluir curso

/* ==============================================
   API: GESTÃO DE HORÁRIOS DE TURMAS
   PREFIXO: /api/horarios
   CONTROLLER: Api\HorarioController
   ============================================== */
use App\Http\Controllers\Api\HorarioController;

Route::get('/horarios', [HorarioController::class, 'index']);         // GET - Listar todos os horários
Route::post('/horarios', [HorarioController::class, 'store']);        // POST - Criar novo horário
Route::get('/horarios/{id}', [HorarioController::class, 'show']);     // GET - Exibir horário específico
Route::put('/horarios/{id}', [HorarioController::class, 'update']);   // PUT - Atualizar horário
Route::delete('/horarios/{id}', [HorarioController::class, 'destroy']); // DELETE - Excluir horário

/* ==============================================
   API: GESTÃO DE FORMADORES
   PREFIXO: /api/formadores
   CONTROLLER: Api\FormadorController
   ============================================== */
use App\Http\Controllers\Api\FormadorController;

Route::get('/formadores', [FormadorController::class, 'index']);      // GET - Listar todos os formadores
Route::post('/formadores', [FormadorController::class, 'store']);     // POST - Criar novo formador
Route::get('/formadores/{id}', [FormadorController::class, 'show']);  // GET - Exibir formador específico
Route::put('/formadores/{id}', [FormadorController::class, 'update']); // PUT - Atualizar formador
Route::delete('/formadores/{id}', [FormadorController::class, 'destroy']); // DELETE - Excluir formador

/* ==============================================
   API: GESTÃO DE PRÉ-INSCRIÇÕES
   PREFIXO: /api/pre-inscricoes
   CONTROLLER: Api\PreInscricaoController
   ============================================== */
use App\Http\Controllers\Api\PreInscricaoController;

Route::post('/pre-inscricoes', [PreInscricaoController::class, 'store']);    // POST - Criar pré-inscrição (público)
Route::get('/pre-inscricoes', [PreInscricaoController::class, 'index']);     // GET - Listar pré-inscrições (admin)
Route::get('/pre-inscricoes/{id}', [PreInscricaoController::class, 'show']); // GET - Exibir pré-inscrição específica
Route::put('/pre-inscricoes/{id}', [PreInscricaoController::class, 'update']); // PUT - Atualizar/aprovar (admin)
Route::delete('/pre-inscricoes/{id}', [PreInscricaoController::class, 'destroy']); // DELETE - Excluir pré-inscrição

/* ==============================================
   API: GESTÃO DE CATEGORIAS (LOJA/SNACK BAR)
   PREFIXO: /api/categorias
   CONTROLLER: CategoriaController
   ============================================== */
use App\Http\Controllers\CategoriaController;

Route::get('/categorias', [CategoriaController::class, 'index']);     // GET - Listar categorias (com filtros)
Route::post('/categorias', [CategoriaController::class, 'store']);    // POST - Criar nova categoria
Route::get('/categorias/{categoria}', [CategoriaController::class, 'show']); // GET - Exibir categoria específica
Route::put('/categorias/{categoria}', [CategoriaController::class, 'update']); // PUT - Atualizar categoria
Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy']); // DELETE - Excluir categoria

/* ==============================================
   API: GESTÃO DE PRODUTOS (LOJA/SNACK BAR)
   PREFIXO: /api/produtos
   CONTROLLER: ProdutoController
   ============================================== */
use App\Http\Controllers\ProdutoController;

// CRUD básico de produtos
Route::get('/produtos', [ProdutoController::class, 'index']);         // GET - Listar produtos (com filtros)
Route::post('/produtos', [ProdutoController::class, 'store']);        // POST - Criar novo produto
Route::get('/produtos/{produto}', [ProdutoController::class, 'show']); // GET - Exibir produto específico
Route::put('/produtos/{produto}', [ProdutoController::class, 'update']); // PUT - Atualizar produto
Route::delete('/produtos/{produto}', [ProdutoController::class, 'destroy']); // DELETE - Excluir produto

/* ==============================================
   API: ROTAS ESPECIAIS PARA SITE PÚBLICO
   DESCRIÇÃO: Endpoints específicos para exibição pública
   ============================================== */
Route::get('/produtos/em-destaque', [ProdutoController::class, 'emDestaque']);               // GET - Produtos em destaque
Route::get('/categorias/{categoria}/produtos', [ProdutoController::class, 'porCategoria']); // GET - Produtos por categoria