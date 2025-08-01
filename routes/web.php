<?php

/* ==============================================
   ARQUIVO: ROTAS WEB
   DESCRIÇÃO: Define todas as rotas HTTP da aplicação
   ESTRUTURA: Rotas públicas + Painel administrativo
   ============================================== */

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/* ==============================================
   ROTA RAIZ - REDIRECIONAMENTO
   ============================================== */
// Redireciona '/' para a página inicial do site público
Route::get('/', function () {
    return redirect()->route('public.home');
});

/* ==============================================
   GRUPO: ROTAS PÚBLICAS DO SITE
   PREFIXO: /site
   DESCRIÇÃO: Páginas acessíveis a todos os visitantes
   ============================================== */
Route::prefix('site')->name('public.')->group(function () {
    
    // PÁGINA INICIAL - Apresentação do centro de formação
    Route::get('/', function () {
        return view('public.home');
    })->name('home');
    
    // CENTROS - Lista de centros de formação disponíveis
    Route::get('/centros', function () {
        return view('public.centros');
    })->name('centros');
    
    // DETALHES DO CENTRO - Informações específicas de um centro
    Route::get('/centro/{id}', function ($id) {
        return view('public.centro-detalhes', compact('id'));
    })->name('centro.detalhes');
    
    // SOBRE NÓS - Informações institucionais
    Route::get('/sobre', function () {
        return view('public.sobre');
    })->name('sobre');
    
    // CONTACTOS - Informações de contacto e localização
    Route::get('/contactos', function () {
        return view('public.contactos');
    })->name('contactos');
});

/* ==============================================
   ROTA: PRÉ-INSCRIÇÃO PÚBLICA
   DESCRIÇÃO: Formulário para interessados se inscreverem
   ============================================== */
Route::get('/inscricao', function () {
    return view('public.pre-inscricao');
})->name('public.inscricao');

/* ==============================================
   GRUPO: AUTENTICAÇÃO
   DESCRIÇÃO: Login e logout do sistema administrativo
   ============================================== */

// PÁGINA DE LOGIN - Formulário de autenticação
Route::get('/login', function () {
    return view('login');
})->name('login');

// PROCESSAR LOGIN - Validação das credenciais
Route::post('/login', function () {
    // TODO: Implementar lógica de autenticação completa
    return redirect()->route('dashboard');
})->name('login.authenticate');

// LOGOUT - Encerrar sessão administrativa
Route::get('/logout', function () {
    // TODO: Implementar lógica de logout completa
    return redirect()->route('login');
})->name('logout');

/* ==============================================
   GRUPO: ROTAS ADMINISTRATIVAS PROTEGIDAS
   MIDDLEWARE: web (sessão básica)
   DESCRIÇÃO: Painel de administração do sistema
   TODO: Implementar middleware de autenticação
   ============================================== */
Route::middleware(['web'])->group(function () {
    
    /* ==============================================
       DASHBOARD - PÁGINA INICIAL DO PAINEL
       ============================================== */
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /* ==============================================
       MÓDULO: GESTÃO DE CURSOS
       PREFIXO: /cursos
       DESCRIÇÃO: CRUD completo para cursos de formação
       ============================================== */
    Route::prefix('cursos')->name('cursos.')->group(function () {
        // Lista todos os cursos com DataTable
        Route::get('/', function () {
            return view('cursos.index');
        })->name('index');
        
        // Formulário para criar novo curso
        Route::get('/criar', function () {
            return view('cursos.create');
        })->name('create');
        
        // Formulário para editar curso existente
        Route::get('/editar/{id}', function ($id) {
            return view('cursos.edit', compact('id'));
        })->name('edit');
    });

    /* ==============================================
       MÓDULO: GESTÃO DE CENTROS
       PREFIXO: /centros
       DESCRIÇÃO: CRUD para centros de formação
       ============================================== */
    Route::prefix('centros')->name('centros.')->group(function () {
        // Lista todos os centros
        Route::get('/', function () {
            return view('centros.index');
        })->name('index');
        
        // Formulário para criar novo centro
        Route::get('/criar', function () {
            return view('centros.create');
        })->name('create');
        
        // Formulário para editar centro existente
        Route::get('/editar/{id}', function ($id) {
            return view('centros.edit', compact('id'));
        })->name('edit');
    });

    /* ==============================================
       MÓDULO: GESTÃO DE FORMADORES
       PREFIXO: /formadores
       DESCRIÇÃO: CRUD para professores/instrutores
       ============================================== */
    Route::prefix('formadores')->name('formadores.')->group(function () {
        // Lista todos os formadores
        Route::get('/', function () {
            return view('formadores.index');
        })->name('index');
        
        // Formulário para criar novo formador
        Route::get('/criar', function () {
            return view('formadores.create');
        })->name('create');
        
        // Formulário para editar formador existente
        Route::get('/editar/{id}', function ($id) {
            return view('formadores.edit', compact('id'));
        })->name('edit');
    });

    /* ==============================================
       MÓDULO: GESTÃO DE HORÁRIOS
       PREFIXO: /horarios
       DESCRIÇÃO: CRUD para horários de turmas
       ============================================== */
    Route::prefix('horarios')->name('horarios.')->group(function () {
        // Lista todos os horários
        Route::get('/', function () {
            return view('horarios.index');
        })->name('index');
        
        // Formulário para criar novo horário
        Route::get('/criar', function () {
            return view('horarios.create');
        })->name('create');
        
        // Formulário para editar horário existente
        Route::get('/editar/{id}', function ($id) {
            return view('horarios.edit', compact('id'));
        })->name('edit');
    });

    /* ==============================================
       MÓDULO: GESTÃO DE PRÉ-INSCRIÇÕES
       PREFIXO: /pre-inscricoes
       DESCRIÇÃO: Gestão de candidaturas de interessados
       ============================================== */
    Route::prefix('pre-inscricoes')->name('pre-inscricoes.')->group(function () {
        // Lista todas as pré-inscrições
        Route::get('/', function () {
            return view('pre-inscricoes.index');
        })->name('index');
        
        // Formulário para criar nova pré-inscrição (admin)
        Route::get('/criar', function () {
            return view('pre-inscricoes.create');
        })->name('create');
        
        // Formulário para editar/aprovar pré-inscrição
        Route::get('/editar/{id}', function ($id) {
            return view('pre-inscricoes.edit', compact('id'));
        })->name('edit');
    });

    /* ==============================================
       MÓDULO: GESTÃO DE CATEGORIAS (LOJA/SNACK)
       PREFIXO: /categorias
       DESCRIÇÃO: CRUD para categorias de produtos
       ============================================== */
    Route::prefix('categorias')->name('categorias.')->group(function () {
        // Lista todas as categorias
        Route::get('/', function () {
            return view('categorias.index');
        })->name('index');
        
        // Formulário para criar nova categoria
        Route::get('/criar', function () {
            return view('categorias.create');
        })->name('create');
        
        // Formulário para editar categoria existente
        Route::get('/editar/{id}', function ($id) {
            return view('categorias.edit', compact('id'));
        })->name('edit');
    });

    /* ==============================================
       MÓDULO: GESTÃO DE PRODUTOS (LOJA/SNACK)
       PREFIXO: /produtos
       DESCRIÇÃO: CRUD para produtos da loja e snack bar
       ============================================== */
    Route::prefix('produtos')->name('produtos.')->group(function () {
        // Lista todos os produtos
        Route::get('/', function () {
            return view('produtos.index');
        })->name('index');
        
        // Formulário para criar novo produto
        Route::get('/criar', function () {
            return view('produtos.create');
        })->name('create');
        
        // Formulário para editar produto existente
        Route::get('/editar/{id}', function ($id) {
            return view('produtos.edit', compact('id'));
        })->name('edit');
    });
});
