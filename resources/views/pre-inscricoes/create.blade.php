@extends('layouts.app')

@section('title', 'Nova Pré-Inscrição')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-plus me-3 text-primary"></i>Nova Pré-Inscrição
                    </h1>
                    <p class="text-muted">Registar uma nova pré-inscrição no sistema</p>
                </div>
                <a href="{{ route('pre-inscricoes.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Informações da Pré-Inscrição
                    </h5>
                </div>
                <div class="card-body">
                    <form id="preInscricaoForm">
                        <!-- Dados Pessoais -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Dados Pessoais
                                </h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nome_completo" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome_completo" name="nome_completo" required maxlength="100">
                                <div class="form-text">Máximo 100 caracteres</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="100">
                                <div class="form-text">Opcional</div>
                            </div>
                        </div>

                        <!-- Contactos Dinâmicos -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-phone me-2"></i>Contactos
                                </h6>
                            </div>
                        </div>

                        <div id="contactosContainer">
                            <div class="row contacto-item">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tipo de Contacto</label>
                                    <select class="form-select contacto-tipo" name="contacto_tipo[]">
                                        <option value="telefone">Telefone</option>
                                        <option value="telemovel">Telemóvel</option>
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="facebook">Facebook</option>
                                        <option value="outro">Outro</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Valor</label>
                                    <input type="text" class="form-control contacto-valor" name="contacto_valor[]" maxlength="50">
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removerContacto(this)" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="adicionarContacto()">
                                    <i class="fas fa-plus me-2"></i>Adicionar Contacto
                                </button>
                            </div>
                        </div>

                        <!-- Curso e Centro -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-graduation-cap me-2"></i>Curso e Localização
                                </h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="curso_id" class="form-label">Curso <span class="text-danger">*</span></label>
                                <select class="form-select" id="curso_id" name="curso_id" required>
                                    <option value="">Selecione o curso</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="centro_id" class="form-label">Centro <span class="text-danger">*</span></label>
                                <select class="form-select" id="centro_id" name="centro_id" required>
                                    <option value="">Selecione o centro</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="horario_id" class="form-label">Horário</label>
                                <select class="form-select" id="horario_id" name="horario_id">
                                    <option value="">Selecione o horário (opcional)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="pendente" selected>Pendente</option>
                                    <option value="confirmado">Confirmado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-sticky-note me-2"></i>Observações
                                </h6>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações Administrativas</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="4" maxlength="1000"></textarea>
                            <div class="form-text">Notas internas sobre a pré-inscrição (máximo 1000 caracteres)</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('pre-inscricoes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Pré-Inscrição
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Ajuda
                    </h6>
                </div>
                <div class="card-body">
                    <h6>Dicas para registar pré-inscrição:</h6>
                    <ul class="small">
                        <li><strong>Nome:</strong> Use o nome completo do candidato</li>
                        <li><strong>Contactos:</strong> Adicione múltiplos contactos se necessário</li>
                        <li><strong>Curso:</strong> Selecione o curso de interesse</li>
                        <li><strong>Centro:</strong> Escolha o centro onde decorrerá a formação</li>
                        <li><strong>Horário:</strong> Opcional - pode ser definido posteriormente</li>
                        <li><strong>Observações:</strong> Use para notas administrativas importantes</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3" id="previewCard" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-eye me-2"></i>Pré-visualização
                    </h6>
                </div>
                <div class="card-body" id="previewContent">
                    <!-- Preview será gerado aqui -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    carregarCursos();
    carregarCentros();
    carregarHorarios();

    // Preview em tempo real
    $('#preInscricaoForm input, #preInscricaoForm select, #preInscricaoForm textarea').on('input change', function() {
        atualizarPreview();
    });

    // Submit do formulário
    $('#preInscricaoForm').on('submit', function(e) {
        e.preventDefault();
        criarPreInscricao();
    });

    // Atualizar horários quando centro mudar
    $('#centro_id').on('change', function() {
        carregarHorarios();
    });
});

function carregarCursos() {
    $.get('/api/cursos', function(data) {
        let options = '<option value="">Selecione o curso</option>';
        data.forEach(function(curso) {
            if (curso.ativo) {
                options += `<option value="${curso.id}">${curso.nome}</option>`;
            }
        });
        $('#curso_id').html(options);
    });
}

function carregarCentros() {
    $.get('/api/centros', function(data) {
        let options = '<option value="">Selecione o centro</option>';
        data.forEach(function(centro) {
            if (centro.ativo) {
                options += `<option value="${centro.id}">${centro.nome}</option>`;
            }
        });
        $('#centro_id').html(options);
    });
}

function carregarHorarios() {
    const centroId = $('#centro_id').val();
    let url = '/api/horarios';
    
    if (centroId) {
        url += `?centro_id=${centroId}`;
    }

    $.get(url, function(data) {
        let options = '<option value="">Selecione o horário (opcional)</option>';
        data.forEach(function(horario) {
            options += `<option value="${horario.id}">${horario.descricao}</option>`;
        });
        $('#horario_id').html(options);
    });
}

function adicionarContacto() {
    const novoContacto = `
        <div class="row contacto-item">
            <div class="col-md-4 mb-3">
                <label class="form-label">Tipo de Contacto</label>
                <select class="form-select contacto-tipo" name="contacto_tipo[]">
                    <option value="telefone">Telefone</option>
                    <option value="telemovel">Telemóvel</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="facebook">Facebook</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Valor</label>
                <input type="text" class="form-control contacto-valor" name="contacto_valor[]" maxlength="50">
            </div>
            <div class="col-md-2 mb-3 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removerContacto(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    $('#contactosContainer').append(novoContacto);
    atualizarBotoesRemover();
    atualizarPreview();
}

function removerContacto(button) {
    $(button).closest('.contacto-item').remove();
    atualizarBotoesRemover();
    atualizarPreview();
}

function atualizarBotoesRemover() {
    const items = $('.contacto-item');
    items.each(function(index) {
        const botaoRemover = $(this).find('button[onclick*="removerContacto"]');
        if (items.length === 1) {
            botaoRemover.prop('disabled', true);
        } else {
            botaoRemover.prop('disabled', false);
        }
    });
}

function atualizarPreview() {
    const nomeCompleto = $('#nome_completo').val();
    const email = $('#email').val();
    const cursoId = $('#curso_id').val();
    const centroId = $('#centro_id').val();
    const status = $('#status').val();

    if (nomeCompleto || email || cursoId || centroId) {
        const statusBadge = getStatusBadge(status);
        const cursoNome = $('#curso_id option:selected').text();
        const centroNome = $('#centro_id option:selected').text();
        
        // Coletar contactos
        let contactosHtml = '';
        $('.contacto-item').each(function() {
            const tipo = $(this).find('.contacto-tipo').val();
            const valor = $(this).find('.contacto-valor').val();
            if (valor) {
                contactosHtml += `<li><strong>${tipo}:</strong> ${valor}</li>`;
            }
        });

        let preview = `
            <h6>${nomeCompleto || 'Nome do Candidato'}</h6>
            <p class="mb-2"><strong>Email:</strong> ${email || '<span class="text-muted">N/A</span>'}</p>
            <p class="mb-2"><strong>Status:</strong> ${statusBadge}</p>
            ${cursoId ? `<p class="mb-2"><strong>Curso:</strong> ${cursoNome}</p>` : ''}
            ${centroId ? `<p class="mb-2"><strong>Centro:</strong> ${centroNome}</p>` : ''}
            ${contactosHtml ? `
                <div class="mt-2">
                    <strong>Contactos:</strong>
                    <ul class="small mb-0">${contactosHtml}</ul>
                </div>
            ` : ''}
        `;

        $('#previewContent').html(preview);
        $('#previewCard').show();
    } else {
        $('#previewCard').hide();
    }
}

function getStatusBadge(status) {
    switch (status) {
        case 'pendente':
            return '<span class="badge bg-warning">Pendente</span>';
        case 'confirmado':
            return '<span class="badge bg-success">Confirmado</span>';
        case 'cancelado':
            return '<span class="badge bg-danger">Cancelado</span>';
        default:
            return '<span class="badge bg-secondary">Desconhecido</span>';
    }
}

function criarPreInscricao() {
    // Coletar contactos
    const contactos = {};
    $('.contacto-item').each(function() {
        const tipo = $(this).find('.contacto-tipo').val();
        const valor = $(this).find('.contacto-valor').val();
        if (valor) {
            contactos[tipo] = valor;
        }
    });

    const formData = {
        nome_completo: $('#nome_completo').val(),
        email: $('#email').val() || null,
        curso_id: parseInt($('#curso_id').val()),
        centro_id: parseInt($('#centro_id').val()),
        horario_id: $('#horario_id').val() ? parseInt($('#horario_id').val()) : null,
        status: $('#status').val(),
        contactos: Object.keys(contactos).length > 0 ? contactos : null,
        observacoes: $('#observacoes').val() || null
    };

    $.ajax({
        url: '/api/pre-inscricoes',
        method: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        beforeSend: function() {
            $('#preInscricaoForm button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        },
        success: function(response) {
            Swal.fire({
                title: 'Sucesso!',
                text: 'Pré-inscrição criada com sucesso!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '{{ route("pre-inscricoes.index") }}';
            });
        },
        error: function(xhr) {
            let message = 'Ocorreu um erro ao criar a pré-inscrição.';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                message = errors.join('<br>');
            }

            Swal.fire({
                title: 'Erro!',
                html: message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        },
        complete: function() {
            $('#preInscricaoForm button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Pré-Inscrição');
        }
    });
}
</script>
@endsection
