@extends('layouts.public')

@section('title', 'Centros de Formação')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('public.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Centros</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Page Header -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center">
            <h1 class="section-title">Nossos Centros de Formação</h1>
            <p class="section-subtitle">
                Escolha o centro mais próximo de si e explore todos os cursos disponíveis. 
                Cada centro oferece infraestruturas modernas e formadores especializados.
            </p>
        </div>
    </div>
</section>

<!-- Centros Grid -->
<section class="section">
    <div class="container">
        <div class="row" id="centros-grid">
            <div class="col-12">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Carregando centros de formação...</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Informações Adicionais -->
<section class="section bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h3 class="mb-4">Não encontra o que procura?</h3>
                <p class="text-muted mb-4">
                    Entre em contacto connosco! A nossa equipa está disponível para esclarecer 
                    todas as suas dúvidas e ajudá-lo a encontrar o curso ideal para os seus objetivos.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('public.contactos') }}" class="btn btn-primary">
                        <i class="fas fa-phone me-2"></i>Contactar-nos
                    </a>
                    <a href="mailto:info@mcformacao.pt" class="btn btn-outline-primary">
                        <i class="fas fa-envelope me-2"></i>Enviar Email
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    carregarCentros();
});

function carregarCentros() {
    $.get('/api/centros', function(data) {
        exibirCentros(data);
    }).fail(function() {
        $('#centros-grid').html(`
            <div class="col-12 text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h5>Erro ao carregar centros</h5>
                <p class="text-muted">Tente novamente mais tarde ou entre em contacto connosco.</p>
                <button class="btn btn-primary" onclick="carregarCentros()">
                    <i class="fas fa-redo me-2"></i>Tentar Novamente
                </button>
            </div>
        `);
    });
}

function exibirCentros(centros) {
    let html = '';
    
    if (centros.length === 0) {
        html = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-building fa-4x text-muted mb-4"></i>
                <h5>Nenhum centro disponível</h5>
                <p class="text-muted">Estamos a trabalhar para adicionar novos centros em breve.</p>
                <a href="{{ route('public.contactos') }}" class="btn btn-primary">
                    <i class="fas fa-info-circle me-2"></i>Mais Informações
                </a>
            </div>
        `;
    } else {
        centros.forEach(function(centro) {
            let contactosHtml = '';
            let emailHtml = '';
            
            // Processar contactos
            try {
                if (centro.contactos) {
                    const contactos = JSON.parse(centro.contactos);
                    if (contactos.length > 0) {
                        contactosHtml = contactos.map(c => 
                            `<span class="badge bg-light text-dark me-1 mb-1">${c.tipo}: ${c.valor}</span>`
                        ).join('');
                    }
                }
            } catch (e) {
                console.log('Erro ao processar contactos:', e);
            }
            
            if (centro.email) {
                emailHtml = `
                    <p class="text-muted mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:${centro.email}" class="text-decoration-none">${centro.email}</a>
                    </p>
                `;
            }
            
            html += `
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white text-center">
                            <h5 class="mb-0">
                                <i class="fas fa-building me-2"></i>${centro.nome}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--accent-color), var(--primary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: white; font-size: 2rem;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            </div>
                            
                            <h6 class="text-center mb-3">${centro.localizacao}</h6>
                            
                            ${emailHtml}
                            
                            ${contactosHtml ? `
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-2">Contactos:</small>
                                    ${contactosHtml}
                                </div>
                            ` : ''}
                            
                            <div class="text-center mt-4">
                                <a href="{{ route('public.centro.detalhes', '') }}/${centro.id}" 
                                   class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-eye me-2"></i>Ver Cursos Disponíveis
                                </a>
                            </div>
                        </div>
                        <div class="card-footer bg-light text-center">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>Seg - Sex: 9h00 - 18h00
                            </small>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#centros-grid').html(html);
}
</script>
@endsection

@section('styles')
<style>
.card-header.bg-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color)) !important;
}

.badge.bg-light {
    border: 1px solid #dee2e6;
}

.card:hover .card-header {
    background: linear-gradient(135deg, var(--accent-color), var(--primary-color)) !important;
}
</style>
@endsection
