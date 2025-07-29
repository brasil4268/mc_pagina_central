@extends('layouts.public')

@section('title', 'Sobre Nós')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('public.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Sobre Nós</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Hero Section -->
<section class="section bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="section-title text-start">Sobre a MC Formação</h1>
                <p class="lead text-muted">
                    Há mais de 10 anos a formar profissionais qualificados e preparados 
                    para os desafios do mercado de trabalho moderno.
                </p>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                     alt="Equipa MC Formação" 
                     class="img-fluid rounded-3 shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- Nossa História -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="text-center mb-5">A Nossa História</h2>
                
                <div class="timeline">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div class="year-badge">2014</div>
                        </div>
                        <div class="col-md-9">
                            <h5>Fundação da MC Formação</h5>
                            <p class="text-muted">
                                Inicio das atividades com foco em formação profissional 
                                nas áreas de tecnologia e gestão.
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div class="year-badge">2017</div>
                        </div>
                        <div class="col-md-9">
                            <h5>Expansão Nacional</h5>
                            <p class="text-muted">
                                Abertura de novos centros em diferentes regiões, 
                                aumentando a nossa capacidade de formação.
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div class="year-badge">2020</div>
                        </div>
                        <div class="col-md-9">
                            <h5>Formação Digital</h5>
                            <p class="text-muted">
                                Implementação de plataforma de e-learning, 
                                adaptando-nos às novas necessidades do mercado.
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div class="year-badge current">2024</div>
                        </div>
                        <div class="col-md-9">
                            <h5>Inovação Contínua</h5>
                            <p class="text-muted">
                                Hoje somos referência em formação profissional, 
                                com mais de 500 alunos formados com sucesso.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Missão, Visão, Valores -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Missão, Visão e Valores</h2>
        </div>
        
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h5 class="card-title">Missão</h5>
                        <p class="card-text">
                            Proporcionar formação de excelência, preparando 
                            profissionais qualificados para responder às 
                            necessidades do mercado de trabalho.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h5 class="card-title">Visão</h5>
                        <p class="card-text">
                            Ser a referência nacional em formação profissional, 
                            reconhecida pela qualidade, inovação e 
                            proximidade com os nossos formandos.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h5 class="card-title">Valores</h5>
                        <p class="card-text">
                            Qualidade, Inovação, Proximidade, 
                            Responsabilidade Social e Compromisso 
                            com o sucesso dos nossos formandos.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Equipa -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">A Nossa Equipa</h2>
            <p class="section-subtitle">
                Profissionais experientes e dedicados ao seu sucesso
            </p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="member-photo mb-3">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                                 alt="Director Geral" 
                                 class="rounded-circle"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <h6>João Silva</h6>
                        <small class="text-muted">Director Geral</small>
                        <p class="small mt-2">
                            15 anos de experiência em gestão 
                            e formação profissional.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="member-photo mb-3">
                            <img src="https://images.unsplash.com/photo-1494790108755-2616b612b47c?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                                 alt="Directora Pedagógica" 
                                 class="rounded-circle"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <h6>Maria Santos</h6>
                        <small class="text-muted">Directora Pedagógica</small>
                        <p class="small mt-2">
                            Especialista em desenvolvimento 
                            curricular e metodologias de ensino.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="member-photo mb-3">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                                 alt="Coordenador Técnico" 
                                 class="rounded-circle"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <h6>Pedro Costa</h6>
                        <small class="text-muted">Coordenador Técnico</small>
                        <p class="small mt-2">
                            Engenheiro com vasta experiência 
                            em tecnologia e inovação.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="member-photo mb-3">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                                 alt="Gestora de Qualidade" 
                                 class="rounded-circle"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <h6>Ana Ferreira</h6>
                        <small class="text-muted">Gestora de Qualidade</small>
                        <p class="small mt-2">
                            Responsável pela garantia de 
                            qualidade em todos os processos.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="section" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);">
    <div class="container text-center text-white">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold mb-4">Junte-se a Nós</h2>
                <p class="lead mb-4">
                    Faça parte da família MC Formação e transforme o seu futuro profissional.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('public.centros') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-graduation-cap me-2"></i>Ver Cursos
                    </a>
                    <a href="{{ route('public.contactos') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-phone me-2"></i>Contactar-nos
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
.year-badge {
    width: 60px;
    height: 60px;
    background: var(--accent-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin: 0 auto;
    position: relative;
}

.year-badge.current {
    background: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.2);
}

.year-badge::after {
    content: '';
    position: absolute;
    top: 60px;
    left: 50%;
    transform: translateX(-50%);
    width: 2px;
    height: 40px;
    background: #e2e8f0;
}

.timeline .row:last-child .year-badge::after {
    display: none;
}

@media (max-width: 768px) {
    .year-badge::after {
        display: none;
    }
}
</style>
@endsection
