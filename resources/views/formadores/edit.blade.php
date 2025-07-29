@extends('layouts.app')

@section('title', 'Editar Formador')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-edit me-3 text-primary"></i>Editar Formador
                    </h1>
                    <p class="text-muted">Atualizar informações do formador</p>
                </div>
                <a href="{{ route('formadores.index') }}" class="btn btn-outline-secondary">
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
                        <i class="fas fa-chalkboard-teacher me-2"></i>Informações do Formador
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-2 text-muted">Carregando dados do formador...</p>
                    </div>

                    <form id="formadorForm" style="display: none;">
                        <input type="hidden" id="formadorId" name="id">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome" name="nome" required maxlength="255">
                                <div class="form-text">Nome completo do formador</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="especialidade" class="form-label">Especialidade</label>
                                <input type="text" class="form-control" id="especialidade" name="especialidade" maxlength="255">
                                <div class="form-text">Área de especialização</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="255">
                                <div class="form-text">Email profissional (opcional)</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="foto_url" class="form-label">URL da Foto</label>
                                <input type="url" class="form-control" id="foto_url" name="foto_url" maxlength="255">
                                <div class="form-text">URL da foto do formador</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Biografia</label>
                            <textarea class="form-control" id="bio" name="bio" rows="6" maxlength="2000"></textarea>
                            <div class="form-text">Biografia profissional, experiência, formação (máximo 2000 caracteres)</div>
                        </div>

                        <!-- Contactos Dinâmicos -->
                        <div class="mb-3">
                            <label class="form-label">Contactos</label>
                            <div id="contactosContainer">
                                <!-- Contactos serão carregados aqui -->
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="adicionarContacto">
                                <i class="fas fa-plus me-2"></i>Adicionar Contacto
                            </button>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('formadores.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Atualizar Formador
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
                    <h6>Dicas para editar um formador:</h6>
                    <ul class="small">
                        <li><strong>Nome:</strong> Use o nome completo do formador</li>
                        <li><strong>Email:</strong> Preferencialmente email profissional</li>
                        <li><strong>Especialidade:</strong> Área principal de atuação</li>
                        <li><strong>Biografia:</strong> Descreva experiência e qualificações</li>
                        <li><strong>Contactos:</strong> Adicione múltiplos meios de contacto</li>
                        <li><strong>Foto:</strong> Use URL de imagem profissional</li>
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
    let contactoIndex = 0;
    const formadorId = {{ request()->route('id') }};

    // Carregar dados do formador
    carregarFormador(formadorId);

    // Preview em tempo real
    $(document).on('input change', '#formadorForm input, #formadorForm select, #formadorForm textarea', function() {
        atualizarPreview();
    });

    // Adicionar contacto
    $('#adicionarContacto').on('click', function() {
        adicionarContacto();
    });

    // Remover contacto (delegação de eventos)
    $(document).on('click', '.remover-contacto', function() {
        $(this).closest('.contacto-item').remove();
        atualizarIndicesContactos();
        atualizarBotoesRemover();
        atualizarPreview();
    });

    // Atualizar preview quando contactos mudam
    $(document).on('input change', '.contacto-tipo, .contacto-valor', function() {
        atualizarPreview();
    });

    // Submit do formulário
    $('#formadorForm').on('submit', function(e) {
        e.preventDefault();
        atualizarFormador();
    });
});

function carregarFormador(id) {
    $.get(`/api/formadores/${id}`)
        .done(function(formador) {
            $('#formadorId').val(formador.id);
            $('#nome').val(formador.nome);
            $('#email').val(formador.email || '');
            $('#especialidade').val(formador.especialidade || '');
            $('#bio').val(formador.bio || '');
            $('#foto_url').val(formador.foto_url || '');

            // Carregar contactos
            carregarContactos(formador.contactos || []);

            $('#formadorForm').show();
            $('.card-body .text-center').hide();
            
            atualizarPreview();
        })
        .fail(function() {
            Swal.fire({
                title: 'Erro!',
                text: 'Não foi possível carregar os dados do formador.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '{{ route("formadores.index") }}';
            });
        });
}

function carregarContactos(contactos) {
    $('#contactosContainer').empty();
    contactoIndex = 0;

    if (contactos.length === 0) {
        adicionarContacto();
    } else {
        contactos.forEach(function(contacto) {
            adicionarContacto(contacto.tipo, contacto.valor);
        });
    }
    
    atualizarBotoesRemover();
}

function adicionarContacto(tipo = '', valor = '') {
    const novoContacto = `
        <div class="contacto-item border rounded p-3 mb-2">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <select class="form-select contacto-tipo" name="contactos[${contactoIndex}][tipo]">
                        <option value="">Tipo de contacto</option>
                        <option value="Telefone" ${tipo === 'Telefone' ? 'selected' : ''}>Telefone</option>
                        <option value="Telemóvel" ${tipo === 'Telemóvel' ? 'selected' : ''}>Telemóvel</option>
                        <option value="WhatsApp" ${tipo === 'WhatsApp' ? 'selected' : ''}>WhatsApp</option>
                        <option value="Email Pessoal" ${tipo === 'Email Pessoal' ? 'selected' : ''}>Email Pessoal</option>
                        <option value="LinkedIn" ${tipo === 'LinkedIn' ? 'selected' : ''}>LinkedIn</option>
                        <option value="Website" ${tipo === 'Website' ? 'selected' : ''}>Website</option>
                        <option value="Outro" ${tipo === 'Outro' ? 'selected' : ''}>Outro</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <input type="text" class="form-control contacto-valor" name="contactos[${contactoIndex}][valor]" placeholder="Valor do contacto" value="${valor}">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remover-contacto">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    $('#contactosContainer').append(novoContacto);
    contactoIndex++;
    atualizarBotoesRemover();
}

function atualizarIndicesContactos() {
    $('#contactosContainer .contacto-item').each(function(index) {
        $(this).find('.contacto-tipo').attr('name', `contactos[${index}][tipo]`);
        $(this).find('.contacto-valor').attr('name', `contactos[${index}][valor]`);
    });
    contactoIndex = $('#contactosContainer .contacto-item').length;
}

function atualizarBotoesRemover() {
    const contactos = $('#contactosContainer .contacto-item');
    contactos.find('.remover-contacto').toggle(contactos.length > 1);
}

function atualizarPreview() {
    const nome = $('#nome').val();
    const email = $('#email').val();
    const especialidade = $('#especialidade').val();
    const foto_url = $('#foto_url').val();
    const bio = $('#bio').val();

    if (nome) {
        const foto = foto_url 
            ? `<img src="${foto_url}" alt="Preview" class="rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.style.display='none'">` 
            : '<i class="fas fa-user-circle fa-4x text-muted mb-2"></i>';

        const emailHtml = email 
            ? `<p class="mb-1"><i class="fas fa-envelope me-2"></i>${email}</p>`
            : '';

        const especialidadeHtml = especialidade 
            ? `<p class="mb-1"><i class="fas fa-star me-2"></i>${especialidade}</p>`
            : '';

        // Coletar contactos
        let contactosHtml = '';
        $('.contacto-item').each(function() {
            const tipo = $(this).find('.contacto-tipo').val();
            const valor = $(this).find('.contacto-valor').val();
            if (tipo && valor) {
                let icon = 'fas fa-phone';
                if (tipo.toLowerCase().includes('email')) icon = 'fas fa-envelope';
                else if (tipo.toLowerCase().includes('whatsapp')) icon = 'fab fa-whatsapp';
                else if (tipo.toLowerCase().includes('linkedin')) icon = 'fab fa-linkedin';
                
                contactosHtml += `<small class="d-block"><i class="${icon} me-1"></i>${tipo}: ${valor}</small>`;
            }
        });

        let preview = `
            <div class="text-center mb-3">${foto}</div>
            <h6 class="text-center">${nome}</h6>
            ${especialidadeHtml}
            ${emailHtml}
            ${contactosHtml ? `<div class="mt-2">${contactosHtml}</div>` : ''}
            ${bio ? `<div class="mt-2"><small class="text-muted">${bio.substring(0, 100)}...</small></div>` : ''}
        `;

        $('#previewContent').html(preview);
        $('#previewCard').show();
    } else {
        $('#previewCard').hide();
    }
}

function atualizarFormador() {
    // Coletar contactos
    const contactos = [];
    $('.contacto-item').each(function() {
        const tipo = $(this).find('.contacto-tipo').val();
        const valor = $(this).find('.contacto-valor').val();
        if (tipo && valor) {
            contactos.push({ tipo, valor });
        }
    });

    const formData = {
        nome: $('#nome').val(),
        email: $('#email').val() || null,
        especialidade: $('#especialidade').val() || null,
        bio: $('#bio').val() || null,
        foto_url: $('#foto_url').val() || null,
        contactos: contactos.length > 0 ? contactos : null
    };

    const formadorId = $('#formadorId').val();

    $.ajax({
        url: `/api/formadores/${formadorId}`,
        method: 'PUT',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        beforeSend: function() {
            $('#formadorForm button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Atualizando...');
        },
        success: function(response) {
            Swal.fire({
                title: 'Sucesso!',
                text: 'Formador atualizado com sucesso!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '{{ route("formadores.index") }}';
            });
        },
        error: function(xhr) {
            let message = 'Ocorreu um erro ao atualizar o formador.';
            
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
            $('#formadorForm button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Atualizar Formador');
        }
    });
}
</script>
@endsection
