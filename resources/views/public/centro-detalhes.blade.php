@extends('layouts.public')

@section('title', 'Centro de Formação')

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('public.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('public.centros') }}">Centros</a></li>
                <li class="breadcrumb-item active" id="breadcrumb-centro">...</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Centro Header -->
<section class="section bg-light" id="centro-header">
    <div class="container">
        <div class="loading text-center">
            <div class="spinner"></div>
            <p>Carregando informações do centro...</p>
        </div>
    </div>
</section>

<!-- Cursos Disponíveis -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="fas fa-book me-2 text-primary"></i>Cursos Disponíveis</h3>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="filtroArea" style="width: auto;">
                            <option value="">Todas as áreas</option>
                        </select>
                        <select class="form-select form-select-sm" id="filtroModalidade" style="width: auto;">
                            <option value="">Todas as modalidades</option>
                            <option value="presencial">Presencial</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                </div>
                
                <div id="cursos-container">
                    <div class="loading text-center">
                        <div class="spinner"></div>
                        <p>Carregando cursos...</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Informações do Centro -->
                <div class="card mb-4" id="info-centro">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações do Centro</h6>
                    </div>
                    <div class="card-body">
                        <div class="loading text-center py-3">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>

                <!-- Formulário de Contacto Rápido -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-phone me-2"></i>Contacto Rápido</h6>
                    </div>
                    <div class="card-body">
                        <form id="contactoRapidoForm">
                            <input type="hidden" id="centro_contacto_id" name="centro_id">
                            <div class="mb-3">
                                <label class="form-label">Nome *</label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="tel" class="form-control" name="telefone">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mensagem *</label>
                                <textarea class="form-control" name="mensagem" rows="3" required placeholder="Gostaria de saber mais sobre..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Mensagem
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de Pré-Inscrição -->
<div class="modal fade" id="preInscricaoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Pré-Inscrição
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="preInscricaoForm">
                    <input type="hidden" id="modal_curso_id" name="curso_id">
                    <input type="hidden" id="modal_centro_id" name="centro_id">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Curso:</strong> <span id="modal_curso_nome"></span><br>
                        <strong>Centro:</strong> <span id="modal_centro_nome"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" name="nome_completo" required maxlength="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" maxlength="100">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Horário Preferido</label>
                        <select class="form-select" name="horario_id" id="modal_horarios">
                            <option value="">Selecionar horário (opcional)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Contactos *</label>
                        <div id="modal-contactos-container">
                            <div class="input-group mb-2">
                                <select class="form-select contacto-tipo" style="max-width: 130px;">
                                    <option value="telefone">Telefone</option>
                                    <option value="telemovel">Telemóvel</option>
                                    <option value="whatsapp">WhatsApp</option>
                                </select>
                                <input type="text" class="form-control contacto-valor" placeholder="Número" required>
                                <button type="button" class="btn btn-outline-success" onclick="adicionarContactoModal()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea class="form-control" name="observacoes" rows="3" maxlength="500" placeholder="Informações adicionais (opcional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="submeterPreInscricao()">
                    <i class="fas fa-paper-plane me-2"></i>Enviar Pré-Inscrição
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Sucesso -->
<div class="modal fade" id="sucessoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Sucesso!
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5>Pré-Inscrição Enviada!</h5>
                <p class="text-muted">Recebemos a sua solicitação e entraremos em contacto em breve.</p>
                <p><strong>Referência:</strong> <span id="referenciaInscricao"></span></p>
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
let centroAtual = null;
let cursosDisponiveis = [];
let horariosDisponiveis = [];

$(document).ready(function() {
    const centroId = {{ request()->route('id') ?? 'null' }};
    
    if (centroId) {
        carregarCentro(centroId);
        carregarCursos(centroId);
        carregarHorarios(centroId);
    } else {
        window.location.href = '{{ route("public.centros") }}';
    }

    // Filtros
    $('#filtroArea, #filtroModalidade').on('change', filtrarCursos);
    
    // Formulário de contacto
    $('#contactoRapidoForm').on('submit', function(e) {
        e.preventDefault();
        enviarContacto();
    });
});

function carregarCentro(id) {
    $.get(`/api/centros/${id}`)
        .done(function(centro) {
            centroAtual = centro;
            exibirCentro(centro);
        })
        .fail(function() {
            $('#centro-header').html(`
                <div class="container text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5>Centro não encontrado</h5>
                    <a href="{{ route('public.centros') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar aos Centros
                    </a>
                </div>
            `);
        });
}

function exibirCentro(centro) {
    // Header
    const headerHtml = `
        <div class="text-center">
            <h1 class="section-title">${centro.nome}</h1>
            <p class="section-subtitle">
                <i class="fas fa-map-marker-alt me-2"></i>${centro.localizacao}
            </p>
        </div>
    `;
    $('#centro-header').html(headerHtml);
    
    // Breadcrumb
    $('#breadcrumb-centro').text(centro.nome);
    
    // Info lateral
    let contactosHtml = '';
    try {
        if (centro.contactos) {
            const contactos = JSON.parse(centro.contactos);
            contactosHtml = contactos.map(c => 
                `<p class="mb-1"><strong>${c.tipo}:</strong> ${c.valor}</p>`
            ).join('');
        }
    } catch (e) {
        console.log('Erro ao processar contactos:', e);
    }
    
    const infoHtml = `
        <h6 class="text-primary">${centro.nome}</h6>
        <p class="mb-2">
            <i class="fas fa-map-marker-alt me-2 text-muted"></i>${centro.localizacao}
        </p>
        ${centro.email ? `
            <p class="mb-2">
                <i class="fas fa-envelope me-2 text-muted"></i>
                <a href="mailto:${centro.email}">${centro.email}</a>
            </p>
        ` : ''}
        ${contactosHtml ? `
            <hr>
            <h6 class="text-muted">Contactos:</h6>
            ${contactosHtml}
        ` : ''}
        <hr>
        <h6 class="text-muted">Horário:</h6>
        <p class="mb-1">Segunda - Sexta: 9h00 - 18h00</p>
        <p class="mb-1">Sábado: 9h00 - 13h00</p>
        <p class="mb-0">Domingo: Encerrado</p>
    `;
    $('#info-centro .card-body').html(infoHtml);
    
    // Set form
    $('#centro_contacto_id').val(centro.id);
}

function carregarCursos(centroId) {
    $.get('/api/cursos', function(cursos) {
        // Filtrar apenas cursos ativos
        cursosDisponiveis = cursos.filter(curso => curso.ativo);
        
        // Preencher filtro de áreas
        const areas = [...new Set(cursosDisponiveis.map(curso => curso.area))];
        $('#filtroArea').html('<option value="">Todas as áreas</option>');
        areas.forEach(area => {
            $('#filtroArea').append(`<option value="${area}">${area}</option>`);
        });
        
        exibirCursos();
    });
}

function carregarHorarios(centroId) {
    $.get('/api/horarios', function(horarios) {
        horariosDisponiveis = horarios.filter(h => h.centro_id == centroId);
    });
}

function filtrarCursos() {
    exibirCursos();
}

function exibirCursos() {
    const areaFiltro = $('#filtroArea').val();
    const modalidadeFiltro = $('#filtroModalidade').val();
    
    let cursosFiltrados = cursosDisponiveis;
    
    if (areaFiltro) {
        cursosFiltrados = cursosFiltrados.filter(curso => curso.area === areaFiltro);
    }
    
    if (modalidadeFiltro) {
        cursosFiltrados = cursosFiltrados.filter(curso => curso.modalidade === modalidadeFiltro);
    }

    let html = '';
    
    if (cursosFiltrados.length === 0) {
        html = `
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5>Nenhum curso encontrado</h5>
                <p class="text-muted">Tente ajustar os filtros ou entre em contacto connosco.</p>
                <button class="btn btn-primary" onclick="$('#filtroArea, #filtroModalidade').val(''); exibirCursos();">
                    <i class="fas fa-redo me-2"></i>Limpar Filtros
                </button>
            </div>
        `;
    } else {
        cursosFiltrados.forEach(curso => {
            const modalidadeBadge = curso.modalidade === 'online' 
                ? '<span class="badge bg-info">Online</span>' 
                : '<span class="badge bg-warning text-dark">Presencial</span>';
            
            const imagem = curso.imagem_url 
                ? `<img src="${curso.imagem_url}" alt="${curso.nome}" class="card-img-top">` 
                : `<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                     <i class="fas fa-book fa-3x text-muted"></i>
                   </div>`;

            html += `
                <div class="card mb-4">
                    <div class="row g-0">
                        <div class="col-md-4">
                            ${imagem}
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title">${curso.nome}</h5>
                                    ${modalidadeBadge}
                                </div>
                                <p class="card-text">
                                    <strong>Área:</strong> ${curso.area}
                                </p>
                                ${curso.descricao ? `
                                    <p class="card-text text-muted">${curso.descricao}</p>
                                ` : ''}
                                ${curso.programa ? `
                                    <details class="mb-3">
                                        <summary class="text-primary" style="cursor: pointer;">Ver programa do curso</summary>
                                        <div class="mt-2 p-3 bg-light rounded">
                                            <pre style="white-space: pre-wrap; font-family: inherit; margin: 0;">${curso.programa}</pre>
                                        </div>
                                    </details>
                                ` : ''}
                                <button class="btn btn-primary" onclick="abrirPreInscricao(${curso.id}, '${curso.nome}')">
                                    <i class="fas fa-user-plus me-2"></i>Fazer Pré-Inscrição
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#cursos-container').html(html);
}

function abrirPreInscricao(cursoId, cursoNome) {
    if (!centroAtual) return;
    
    $('#modal_curso_id').val(cursoId);
    $('#modal_centro_id').val(centroAtual.id);
    $('#modal_curso_nome').text(cursoNome);
    $('#modal_centro_nome').text(centroAtual.nome);
    
    // Carregar horários para este curso
    const horariosEste = horariosDisponiveis.filter(h => h.curso_id == cursoId);
    let horariosHtml = '<option value="">Selecionar horário (opcional)</option>';
    
    horariosEste.forEach(horario => {
        const horaTexto = horario.hora_inicio && horario.hora_fim 
            ? ` (${horario.hora_inicio} - ${horario.hora_fim})`
            : '';
        horariosHtml += `<option value="${horario.id}">${horario.dia_semana} - ${horario.periodo}${horaTexto}</option>`;
    });
    
    $('#modal_horarios').html(horariosHtml);
    
    // Limpar form
    $('#preInscricaoForm')[0].reset();
    $('#modal-contactos-container').html(`
        <div class="input-group mb-2">
            <select class="form-select contacto-tipo" style="max-width: 130px;">
                <option value="telefone">Telefone</option>
                <option value="telemovel">Telemóvel</option>
                <option value="whatsapp">WhatsApp</option>
            </select>
            <input type="text" class="form-control contacto-valor" placeholder="Número" required>
            <button type="button" class="btn btn-outline-success" onclick="adicionarContactoModal()">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    `);
    
    $('#preInscricaoModal').modal('show');
}

function adicionarContactoModal() {
    const novoContacto = `
        <div class="input-group mb-2">
            <select class="form-select contacto-tipo" style="max-width: 130px;">
                <option value="telefone">Telefone</option>
                <option value="telemovel">Telemóvel</option>
                <option value="whatsapp">WhatsApp</option>
            </select>
            <input type="text" class="form-control contacto-valor" placeholder="Número" required>
            <button type="button" class="btn btn-outline-danger" onclick="$(this).closest('.input-group').remove()">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    $('#modal-contactos-container').append(novoContacto);
}

function submeterPreInscricao() {
    const contactos = [];
    $('#modal-contactos-container .input-group').each(function() {
        const tipo = $(this).find('.contacto-tipo').val();
        const valor = $(this).find('.contacto-valor').val();
        if (valor.trim()) {
            contactos.push({ tipo, valor });
        }
    });

    if (contactos.length === 0) {
        alert('Adicione pelo menos um contacto.');
        return;
    }

    const dados = {
        curso_id: parseInt($('#modal_curso_id').val()),
        centro_id: parseInt($('#modal_centro_id').val()),
        horario_id: $('#modal_horarios').val() ? parseInt($('#modal_horarios').val()) : null,
        nome_completo: $('input[name="nome_completo"]').val(),
        email: $('input[name="email"]').val() || null,
        contactos: JSON.stringify(contactos),
        observacoes: $('textarea[name="observacoes"]').val() || null
    };

    $.ajax({
        url: '/api/pre-inscricoes',
        method: 'POST',
        data: JSON.stringify(dados),
        contentType: 'application/json',
        success: function(response) {
            $('#preInscricaoModal').modal('hide');
            $('#referenciaInscricao').text(`#${response.id}`);
            $('#sucessoModal').modal('show');
        },
        error: function(xhr) {
            alert('Erro ao enviar pré-inscrição. Tente novamente.');
        }
    });
}

function enviarContacto() {
    // Simulação de envio de contacto
    alert('Mensagem enviada! Entraremos em contacto em breve.');
    $('#contactoRapidoForm')[0].reset();
}
</script>
@endsection
