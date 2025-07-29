<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - MC-COMERCIAL</title>
    <meta name="description" content="Centro de formação profissional especializado em diversas áreas do conhecimento">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #334155;
            --accent-color: #3b82f6;
            --light-gray: #f1f5f9;
            --dark-gray: #475569;
            --white: #ffffff;
            --text-color: #1e293b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
        }
        
        /* Header */
        .main-header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .top-bar {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 0;
            font-size: 0.875rem;
        }
        
        .navbar {
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color) !important;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--text-color) !important;
            padding: 0.75rem 1.25rem !important;
            transition: all 0.3s ease;
            border-radius: 0.375rem;
            margin: 0 0.25rem;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary-color) !important;
            background-color: var(--light-gray);
        }
        
        .btn-login {
            background: var(--accent-color);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }
        
        /* Main Content */
        .main-content {
            margin-top: 140px;
            min-height: calc(100vh - 140px);
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,0 1000,0 1000,100 0,20"/></svg>');
            background-size: cover;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card-img-top {
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .card:hover .card-img-top {
            transform: scale(1.05);
        }
        
        /* Buttons */
        .btn {
            border-radius: 0.5rem;
            font-weight: 600;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .btn-primary {
            background: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .btn-outline-primary {
            color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-outline-primary:hover {
            background: var(--accent-color);
            border-color: var(--accent-color);
            transform: translateY(-2px);
        }
        
        /* Sections */
        .section {
            padding: 4rem 0;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent-color);
            border-radius: 2px;
        }
        
        .section-subtitle {
            font-size: 1.125rem;
            color: var(--dark-gray);
            margin-bottom: 3rem;
        }
        
        /* Features Grid */
        .feature-card {
            text-align: center;
            padding: 2rem;
            height: 100%;
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--light-gray);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: var(--accent-color);
            transition: all 0.3s ease;
        }
        
        .card:hover .feature-icon {
            background: var(--accent-color);
            color: white;
            transform: scale(1.1);
        }
        
        /* Footer */
        .footer {
            background: var(--secondary-color);
            color: white;
            padding: 3rem 0 1rem;
        }
        
        .footer h5 {
            color: white;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s ease;
            display: block;
            padding: 0.25rem 0;
        }
        
        .footer-link:hover {
            color: white;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            background: var(--accent-color);
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .copyright {
            border-top: 1px solid #475569;
            margin-top: 2rem;
            padding-top: 2rem;
            text-align: center;
            color: #94a3b8;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-top: 120px;
            }
            
            .hero-section {
                padding: 2rem 0;
            }
            
            .section {
                padding: 2rem 0;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }
        
        /* Breadcrumb */
        .breadcrumb {
            background: var(--light-gray);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .breadcrumb-item a {
            color: var(--accent-color);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: var(--dark-gray);
        }
        
        /* Loading */
        .loading {
            text-align: center;
            padding: 3rem;
            color: var(--dark-gray);
        }
        
        .spinner {
            width: 3rem;
            height: 3rem;
            border: 0.25rem solid var(--light-gray);
            border-top: 0.25rem solid var(--accent-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Badge */
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            font-weight: 600;
        }
        
        /* Service Cards */
        .service-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            background: white;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card-img-container {
            position: relative;
            overflow: hidden;
            height: 250px;
        }
        
        .service-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .service-card:hover .service-img {
            transform: scale(1.1);
        }
        
        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(30, 58, 138, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .service-card:hover .card-overlay {
            opacity: 1;
        }
        
        .card-overlay .feature-icon {
            background: white;
            color: var(--primary-color);
            transform: scale(1.2);
        }
        
        .cta-button {
            background: var(--accent-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .service-card:hover .cta-button {
            background: var(--primary-color);
            transform: translateY(-2px);
        }
        
        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            border-radius: 1rem 1rem 0 0;
            border-bottom: none;
            padding: 1.5rem 2rem;
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1.5rem 2rem;
        }
        
        /* Service Items */
        .service-item {
            padding: 1rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
        }
        
        .service-item:hover {
            background-color: var(--light-gray);
        }
        
        .feature-icon-small {
            width: 40px;
            height: 40px;
            background: var(--light-gray);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color);
            font-size: 1.2rem;
        }
        
        /* Product Cards */
        .produto-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 0.75rem;
            overflow: hidden;
        }
        
        .produto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .produto-img {
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .produto-card:hover .produto-img {
            transform: scale(1.05);
        }
        
        /* Menu Items */
        .menu-categoria {
            margin-bottom: 2rem;
        }
        
        .menu-item {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef !important;
        }
        
        .menu-item:hover {
            border-color: var(--accent-color) !important;
            background-color: var(--light-gray);
        }
        
        .menu-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            flex-shrink: 0;
        }
        
        /* Nav Pills */
        .nav-pills .nav-link {
            border-radius: 0.5rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .nav-pills .nav-link:not(.active):hover {
            background-color: var(--light-gray);
            color: var(--primary-color);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-img-container {
                height: 200px;
            }
            
            .modal-body {
                padding: 1rem;
            }
            
            .nav-pills {
                flex-wrap: wrap;
            }
            
            .nav-pills .nav-link {
                margin: 0.25rem;
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }
            
            .menu-item {
                flex-direction: column;
                text-align: center;
            }
            
            .menu-img {
                width: 100px;
                height: 100px;
                margin-bottom: 1rem;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="fas fa-envelope me-2"></i>info@mcformacao.pt
                    <span class="mx-3">|</span>
                    <i class="fas fa-phone me-2"></i>+351 123 456 789
                </div>
                <div class="col-md-6 text-md-end">
                    <i class="fas fa-clock me-2"></i>Seg - Sex: 9h00 - 18h00
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="{{ route('public.home') }}">
                    <i class="fas fa-graduation-cap me-2"></i>MC-COMERCIAL
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('public.home') ? 'active' : '' }}" href="{{ route('public.home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('public.centros*') ? 'active' : '' }}" href="{{ route('public.centros') }}">Centros</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('public.sobre') ? 'active' : '' }}" href="{{ route('public.sobre') }}">Sobre Nós</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('public.contactos') ? 'active' : '' }}" href="{{ route('public.contactos') }}">Contactos</a>
                        </li>
                    </ul>
                    
                    <a href="{{ route('login') }}" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>login
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="fas fa-graduation-cap me-2"></i>MC-COMERCIAL</h5>
                    <p class="text-muted">
                        Centro de formação profissional com mais de 10 anos de experiência 
                        na preparação de profissionais qualificados para o mercado de trabalho.
                    </p>
                    <div class="social-links mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Links Rápidos</h5>
                    <a href="{{ route('public.home') }}" class="footer-link">Home</a>
                    <a href="{{ route('public.centros') }}" class="footer-link">Centros</a>
                    <a href="{{ route('public.sobre') }}" class="footer-link">Sobre Nós</a>
                    <a href="{{ route('public.contactos') }}" class="footer-link">Contactos</a>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Contactos</h5>
                    <p class="footer-link mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Rua A, Bairro 1º de Maio <br> Nº 05, 1º Andar, Luanda-Viana
                    </p>
                    <p class="footer-link mb-2">
                        <i class="fas fa-phone me-2"></i>+244 929-643-510
                        <i class="fas fa-phone me-2"></i>+244 928-966-002
                    </p>
                    <p class="footer-link mb-2">
                        <i class="fas fa-envelope me-2"></i>mucuanha.chineva@gmail.com
                    </p>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Horário de Funcionamento</h5>
                    <p class="footer-link mb-1">Segunda - Sexta: 8h00 - 18h00</p>
                    <p class="footer-link mb-1">Sábado: 9h00 - 16h00</p>
                    <p class="footer-link mb-3">Domingo: Encerrado</p>
                    
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-user-shield me-2"></i>login
                    </a>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; {{ date('Y') }} MC-COMERCIAL. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <script>
        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Active navigation
        $(document).ready(function() {
            $('.navbar-nav .nav-link').each(function() {
                if (this.href === window.location.href) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>
