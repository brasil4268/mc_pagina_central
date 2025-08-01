<!DOCTYPE html>
<html lang="pt">
<head>
    <!-- ==============================================
         META TAGS E CONFIGURAÇÕES BÁSICAS DA APLICAÇÃO ADMINISTRATIVA
         ============================================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MC Formação - @yield('title')</title>
    <!-- Token CSRF para requisições AJAX e formulários -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- ==============================================
         BIBLIOTECAS CSS EXTERNAS PARA PAINEL ADMINISTRATIVO
         ============================================== -->
    <!-- Bootstrap CSS - Framework principal para layout e componentes -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome - Biblioteca de ícones para interface administrativa -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS - Plugin para tabelas interativas com paginação, busca e ordenação -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- ==============================================
         ESTILOS CSS PERSONALIZADOS PARA PAINEL ADMINISTRATIVO
         ============================================== -->
    <style>
        /* ===========================================
           VARIÁVEIS CSS GLOBAIS - CORES DO PAINEL ADMINISTRATIVO
           =========================================== */
        :root {
            --primary-color: #1e40af;      /* Azul principal do painel */
            --secondary-color: #64748b;    /* Cinza para elementos secundários */
            --success-color: #16a34a;      /* Verde para mensagens de sucesso */
            --danger-color: #dc2626;       /* Vermelho para alertas e exclusões */
            --warning-color: #ca8a04;      /* Amarelo para avisos */
            --light-color: #f8fafc;        /* Cor de fundo clara */
            --dark-color: #1e293b;         /* Cor escura para texto */
        }
        
        /* ===========================================
           CONFIGURAÇÕES GERAIS DO BODY
           =========================================== */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
        }
        
        /* ===========================================
           ESTILOS DA MARCA/LOGO NO NAVBAR
           =========================================== */
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        /* ===========================================
           BARRA LATERAL (SIDEBAR) - MENU PRINCIPAL
           =========================================== */
        .sidebar {
            /* Gradiente azul para fundo da sidebar */
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;              /* Largura da sidebar */
            z-index: 1000;
            padding-top: 60px;         /* Espaço para o navbar fixo */
        }
        
        /* Links do menu na sidebar */
        .sidebar .nav-link {
            color: white;
            border-radius: 0.5rem;
            margin: 0.25rem 1rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        /* Estados hover e active dos links da sidebar */
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);    /* Efeito de deslizar para direita */
        }
        
        /* ===========================================
           ÁREA PRINCIPAL DE CONTEÚDO
           =========================================== */
        .main-content {
            margin-left: 250px;           /* Compensar largura da sidebar */
            padding: 2rem;
            min-height: 100vh;
        }
        
        /* ===========================================
           ESTILOS DOS CARTÕES (CARDS)
           =========================================== */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }
        
        /* Efeito hover nos cartões */
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        /* ===========================================
           ESTILOS DOS BOTÕES
           =========================================== */
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        /* Efeito hover nos botões */
        .btn:hover {
            transform: translateY(-1px);
        }
        
        /* Botão primário */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        /* ===========================================
           BARRA DE NAVEGAÇÃO SUPERIOR (NAVBAR)
           =========================================== */
        .navbar {
            background: white !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1001;              /* Acima da sidebar */
        }
        
        /* ===========================================
           ESTILOS DAS TABELAS
           =========================================== */
        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        /* Cabeçalho das tabelas */
        .table th {
            background-color: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }
        
        /* ===========================================
           BADGES E ALERTAS
           =========================================== */
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
        }
        
        .alert {
            border: none;
            border-radius: 0.75rem;
        }
        
        /* ===========================================
           RESPONSIVIDADE PARA DISPOSITIVOS MÓVEIS
           =========================================== */
        @media (max-width: 768px) {
            /* Esconder sidebar em mobile por padrão */
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            /* Mostrar sidebar quando classe .show for adicionada */
            .sidebar.show {
                transform: translateX(0);
            }
            
            /* Conteúdo principal sem margem em mobile */
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- ==============================================
         BARRA DE NAVEGAÇÃO SUPERIOR FIXA
         ============================================== -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <!-- Botão hambúrguer para abrir sidebar em dispositivos móveis -->
            <button class="btn btn-outline-primary d-md-none" type="button" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Logo e nome da aplicação - link para dashboard -->
            <a class="navbar-brand ms-3" href="{{ route('dashboard') }}">
                <!-- Ícone de graduação - representa formação/educação -->
                <i class="fas fa-graduation-cap me-2"></i>MC Formação
            </a>
            
            <!-- Menu do usuário - lado direito -->
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <!-- Botão dropdown do perfil do administrador -->
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>Administrador
                    </a>
                    <!-- Menu dropdown com opções do usuário -->
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt me-2"></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- ==============================================
         BARRA LATERAL - MENU PRINCIPAL DE NAVEGAÇÃO
         ============================================== -->
    <nav class="sidebar" id="sidebar">
        <div class="nav flex-column">
            <!-- Dashboard - Página inicial do painel -->
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            
            <!-- =======  SEÇÃO: GESTÃO EDUCACIONAL  ======= -->
            <!-- Gestão de Cursos - CRUD de cursos oferecidos -->
            <a class="nav-link {{ request()->routeIs('cursos.*') ? 'active' : '' }}" href="{{ route('cursos.index') }}">
                <i class="fas fa-book me-2"></i>Cursos
            </a>
            
            <!-- Gestão de Centros - CRUD de centros de formação -->
            <a class="nav-link {{ request()->routeIs('centros.*') ? 'active' : '' }}" href="{{ route('centros.index') }}">
                <i class="fas fa-building me-2"></i>Centros
            </a>
            
            <!-- Gestão de Formadores - CRUD de professores/instrutores -->
            <a class="nav-link {{ request()->routeIs('formadores.*') ? 'active' : '' }}" href="{{ route('formadores.index') }}">
                <i class="fas fa-chalkboard-teacher me-2"></i>Formadores
            </a>
            
            <!-- Gestão de Horários - CRUD de horários das turmas -->
            <a class="nav-link {{ request()->routeIs('horarios.*') ? 'active' : '' }}" href="{{ route('horarios.index') }}">
                <i class="fas fa-clock me-2"></i>Horários
            </a>
            
            <!-- Gestão de Pré-Inscrições - Lista de interessados nos cursos -->
            <a class="nav-link {{ request()->routeIs('pre-inscricoes.*') ? 'active' : '' }}" href="{{ route('pre-inscricoes.index') }}">
                <i class="fas fa-user-plus me-2"></i>Pré-Inscrições
            </a>
            
            <!-- Separador visual entre seções -->
            <hr class="my-3" style="border-color: rgba(255,255,255,0.3);">
            
            <!-- =======  SEÇÃO: LOJA & SNACK BAR  ======= -->
            <div class="px-3 mb-2">
                <small class="text-light opacity-75">LOJA & SNACK BAR</small>
            </div>
            
            <!-- Gestão de Categorias - CRUD de categorias de produtos -->
            <a class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}" href="{{ route('categorias.index') }}">
                <i class="fas fa-tags me-2"></i>Categorias
            </a>
            
            <!-- Gestão de Produtos - CRUD de produtos da loja/snack bar -->
            <a class="nav-link {{ request()->routeIs('produtos.*') ? 'active' : '' }}" href="{{ route('produtos.index') }}">
                <i class="fas fa-box me-2"></i>Produtos
            </a>
        </div>
    </nav>

    <!-- ==============================================
         ÁREA PRINCIPAL DE CONTEÚDO
         ============================================== -->
    <main class="main-content">
        <!-- =======  ALERTAS DE FEEDBACK DO SISTEMA  ======= -->
        
        <!-- Alerta de sucesso - exibido após operações bem-sucedidas -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Alerta de erro - exibido quando ocorrem erros -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Conteúdo dinâmico das páginas - definido nas views filhas -->
        @yield('content')
    </main>

    <!-- ==============================================
         SCRIPTS JAVASCRIPT PARA FUNCIONALIDADE DO PAINEL
         ============================================== -->
    
    <!-- Bootstrap JS - Componentes interativos (dropdowns, modais, tooltips) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery - Biblioteca base para manipulação DOM e AJAX -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables - Plugin para tabelas avançadas -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- DataTables Bootstrap integration - Estilização Bootstrap para DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 - Alertas e confirmações elegantes -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Tradução portuguesa para DataTables - Localização da interface -->
    <script src="{{ asset('js/datatables-pt.js') }}"></script>
    
    <!-- ==============================================
         FUNÇÕES JAVASCRIPT PERSONALIZADAS
         ============================================== -->
    <script>
        /* ===========================================
           FUNÇÃO: ALTERNAR SIDEBAR EM DISPOSITIVOS MÓVEIS
           =========================================== */
        function toggleSidebar() {
            // Adiciona/remove classe 'show' para mostrar/esconder sidebar
            document.getElementById('sidebar').classList.toggle('show');
        }

        /* ===========================================
           CONFIGURAÇÃO GLOBAL: TOKEN CSRF PARA AJAX
           =========================================== */
        // Configura token CSRF automaticamente em todas as requisições AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        /* ===========================================
           FUNÇÃO: CONFIRMAÇÃO DE EXCLUSÃO COM SWEETALERT
           =========================================== */
        function confirmDelete(url, message = 'Esta ação não pode ser desfeita!') {
            // Exibe modal de confirmação estilizado com SweetAlert2
            Swal.fire({
                title: 'Tem certeza?',
                text: message,
                icon: 'warning',                    // Ícone de aviso
                showCancelButton: true,
                confirmButtonColor: '#dc2626',      // Vermelho para confirmar exclusão
                cancelButtonColor: '#64748b',       // Cinza para cancelar
                confirmButtonText: 'Sim, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                // Se confirmado, redireciona para URL de exclusão
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>
    
    <!-- Seção para scripts específicos de cada página -->
    @yield('scripts')
</body>
</html>
