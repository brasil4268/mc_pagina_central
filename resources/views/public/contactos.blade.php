@extends('layouts.public')

@section('title', 'Contactos')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('public.home') }}">Home</a></li>
                <li class="breadcrumb-item active">Contactos</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Page Header -->
<section class="section bg-light">
    <div class="container text-center">
        <h1 class="section-title">Entre em Contacto</h1>
        <p class="section-subtitle">
            Estamos aqui para esclarecer todas as suas dúvidas e ajudá-lo a escolher 
            o melhor caminho para o seu desenvolvimento profissional.
        </p>
    </div>
</section>

<!-- Contactos Principais -->
<section class="section">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h5>Telefone</h5>
                        <p class="text-muted mb-2">Ligue-nos durante o horário de funcionamento</p>
                        <p class="fw-bold">+351 123 456 789</p>
                        <small class="text-muted">Seg - Sex: 9h00 - 18h00</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h5>Email</h5>
                        <p class="text-muted mb-2">Envie-nos um email e responderemos em breve</p>
                        <p class="fw-bold">
                            <a href="mailto:info@mcformacao.pt">info@mcformacao.pt</a>
                        </p>
                        <small class="text-muted">Resposta em até 24h</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h5>Sede Principal</h5>
                        <p class="text-muted mb-2">Visite-nos na nossa sede</p>
                        <p class="fw-bold">Rua Principal, 123<br>1000-001 Lisboa</p>
                        <small class="text-muted">Estacionamento disponível</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-paper-plane me-2"></i>Envie-nos uma Mensagem
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="contactoForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" name="nome" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefone</label>
                                    <input type="tel" class="form-control" name="telefone">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Assunto *</label>
                                    <select class="form-select" name="assunto" required>
                                        <option value="">Selecione um assunto</option>
                                        <option value="informacoes">Informações sobre cursos</option>
                                        <option value="inscricao">Pré-inscrição</option>
                                        <option value="horarios">Horários e localização</option>
                                        <option value="certificacao">Certificação</option>
                                        <option value="outro">Outro</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Centro de Interesse</label>
                                <select class="form-select" name="centro_id" id="centroSelect">
                                    <option value="">Selecione um centro (opcional)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mensagem *</label>
                                <textarea class="form-control" name="mensagem" rows="5" required 
                                          placeholder="Descreva a sua dúvida ou pedido de informação..."></textarea>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter">
                                <label class="form-check-label" for="newsletter">
                                    Quero receber informações sobre novos cursos e promoções
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Mensagem
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-clock me-2"></i>Horário de Atendimento
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Segunda a Sexta:</strong><br>9h00 - 18h00</p>
                        <p class="mb-2"><strong>Sábado:</strong><br>9h00 - 13h00</p>
                        <p class="mb-0"><strong>Domingo:</strong><br>Encerrado</p>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Fora do horário de funcionamento, utilize o formulário 
                            ou envie um email.
                        </small>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-question-circle me-2"></i>Perguntas Frequentes
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Como fazer uma pré-inscrição?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Pode fazer a pré-inscrição através da página do centro, 
                                        ou contactando-nos diretamente.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Que certificação recebo?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Todos os nossos cursos incluem certificado de participação 
                                        reconhecido pelo mercado.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Qual o preço dos cursos?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Os preços variam conforme o curso e centro. 
                                        Contacte-nos para informações específicas.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-share-alt me-2"></i>Redes Sociais
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="btn btn-outline-info btn-sm">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="btn btn-outline-danger btn-sm">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-outline-dark btn-sm">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Siga-nos para estar sempre atualizado
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mapa/Localização -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Onde nos Encontrar</h2>
            <p class="section-subtitle">Visite-nos nos nossos centros de formação</p>
        </div>
        
        <div class="row" id="centros-localizacao">
            <div class="col-12">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Carregando localizações...</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de Sucesso -->
<div class="modal fade" id="sucessoContactoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Mensagem Enviada!
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5>Obrigado pelo seu contacto!</h5>
                <p class="text-muted">
                    Recebemos a sua mensagem e entraremos em contacto 
                    consigo no prazo máximo de 24 horas.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    carregarCentros();
    
    $('#contactoForm').on('submit', function(e) {
        e.preventDefault();
        enviarMensagem();
    });
});

function carregarCentros() {
    $.get('/api/centros', function(data) {
        // Preencher select de centros
        $('#centroSelect').html('<option value="">Selecione um centro (opcional)</option>');
        data.forEach(centro => {
            $('#centroSelect').append(`<option value="${centro.id}">${centro.nome} - ${centro.localizacao}</option>`);
        });
        
        // Exibir centros na seção de localização
        exibirCentrosLocalizacao(data);
    });
}

function exibirCentrosLocalizacao(centros) {
    let html = '';
    
    if (centros.length === 0) {
        html = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                <p class="text-muted">Informações de localização em breve.</p>
            </div>
        `;
    } else {
        centros.forEach(centro => {
            let contactosHtml = '';
            try {
                if (centro.contactos) {
                    const contactos = JSON.parse(centro.contactos);
                    contactosHtml = contactos.map(c => 
                        `<small class="d-block text-muted">${c.tipo}: ${c.valor}</small>`
                    ).join('');
                }
            } catch (e) {
                console.log('Erro ao processar contactos:', e);
            }
            
            html += `
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 1.5rem;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h6>${centro.nome}</h6>
                            <p class="text-muted mb-2">${centro.localizacao}</p>
                            ${centro.email ? `<small class="d-block text-muted mb-1">${centro.email}</small>` : ''}
                            ${contactosHtml}
                            <div class="mt-3">
                                <a href="{{ route('public.centro.detalhes', '') }}/${centro.id}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#centros-localizacao').html(html);
}

function enviarMensagem() {
    // Simulação de envio de mensagem
    $('#sucessoContactoModal').modal('show');
    $('#contactoForm')[0].reset();
}
</script>
@endsection
