<?php

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

// Redirecionar a raiz para o site público
Route::get('/', function () {
    return redirect()->route('public.home');
});

// Rotas Públicas
Route::prefix('site')->name('public.')->group(function () {
    Route::get('/', function () {
        return view('public.home');
    })->name('home');
    
    Route::get('/centros', function () {
        return view('public.centros');
    })->name('centros');
    
    Route::get('/centro/{id}', function ($id) {
        return view('public.centro-detalhes', compact('id'));
    })->name('centro.detalhes');
    
    Route::get('/sobre', function () {
        return view('public.sobre');
    })->name('sobre');
    
    Route::get('/contactos', function () {
        return view('public.contactos');
    })->name('contactos');
});

// Página pública de pré-inscrição (mantida para compatibilidade)
Route::get('/inscricao', function () {
    return view('public.pre-inscricao');
})->name('public.inscricao');

// Rotas de Autenticação
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function () {
    // Lógica de autenticação simples (será implementada depois)
    return redirect()->route('dashboard');
})->name('login.authenticate');

Route::get('/logout', function () {
    // Lógica de logout
    return redirect()->route('login');
})->name('logout');

// Rotas protegidas (requer autenticação)
Route::middleware(['web'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rotas para Cursos
    Route::prefix('cursos')->name('cursos.')->group(function () {
        Route::get('/', function () {
            return view('cursos.index');
        })->name('index');
        
        Route::get('/criar', function () {
            return view('cursos.create');
        })->name('create');
        
        Route::get('/editar/{id}', function ($id) {
            return view('cursos.edit', compact('id'));
        })->name('edit');
    });

    // Rotas para Centros
    Route::prefix('centros')->name('centros.')->group(function () {
        Route::get('/', function () {
            return view('centros.index');
        })->name('index');
        
        Route::get('/criar', function () {
            return view('centros.create');
        })->name('create');
        
        Route::get('/editar/{id}', function ($id) {
            return view('centros.edit', compact('id'));
        })->name('edit');
    });

    // Rotas para Formadores
    Route::prefix('formadores')->name('formadores.')->group(function () {
        Route::get('/', function () {
            return view('formadores.index');
        })->name('index');
        
        Route::get('/criar', function () {
            return view('formadores.create');
        })->name('create');
        
        Route::get('/editar/{id}', function ($id) {
            return view('formadores.edit', compact('id'));
        })->name('edit');
    });

    // Rotas para Horários
    Route::prefix('horarios')->name('horarios.')->group(function () {
        Route::get('/', function () {
            return view('horarios.index');
        })->name('index');
        
        Route::get('/criar', function () {
            return view('horarios.create');
        })->name('create');
        
        Route::get('/editar/{id}', function ($id) {
            return view('horarios.edit', compact('id'));
        })->name('edit');
    });

    // Rotas para Pré-Inscrições
    Route::prefix('pre-inscricoes')->name('pre-inscricoes.')->group(function () {
        Route::get('/', function () {
            return view('pre-inscricoes.index');
        })->name('index');
        
        Route::get('/criar', function () {
            return view('pre-inscricoes.create');
        })->name('create');
        
        Route::get('/editar/{id}', function ($id) {
            return view('pre-inscricoes.edit', compact('id'));
        })->name('edit');
    });

    // Rotas para Categorias
    Route::prefix('categorias')->name('categorias.')->group(function () {
        Route::get('/', function () {
            return view('categorias.index');
        })->name('index');
        
        Route::get('/criar', function () {
            return view('categorias.create');
        })->name('create');
        
        Route::get('/editar/{id}', function ($id) {
            return view('categorias.edit', compact('id'));
        })->name('edit');
    });

    // Rotas para Produtos
    Route::prefix('produtos')->name('produtos.')->group(function () {
        Route::get('/', function () {
            return view('produtos.index');
        })->name('index');
        
        Route::get('/criar', function () {
            return view('produtos.create');
        })->name('create');
        
        Route::get('/editar/{id}', function ($id) {
            return view('produtos.edit', compact('id'));
        })->name('edit');
    });
});
