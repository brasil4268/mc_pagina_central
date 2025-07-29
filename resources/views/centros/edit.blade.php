@extends('layouts.app')

@section('title', 'Editar Centro')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-edit me-3 text-primary"></i>Editar Centro
                    </h1>
                    <p class="text-muted">Atualizar informações do centro de formação</p>
                </div>
                <a href="{{ route('centros.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>Informações do Centro
                    </h5>
                </div>
                <div class="card-body">
                    <div id="loadingContent" class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                        <p>Carregando dados do centro...</p>
                    </div>

                    <form id="centroForm" style="display: none;">
                        <input type="hidden" id="centro_id" name="id">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nome" class="form-label">Nome do Centro <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome" name="nome" required maxlength="255">
                                <div class="form-text">Nome único do centro (máximo 255 caracteres)</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="255">
                                <div class="form-text">Email do centro (opcional)</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="localizacao" class="form-label">Localização <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="localizacao" name="localizacao" required maxlength="255">
                            <div class="form-text">Endereço completo ou localização do centro</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contactos</label>
                            <div id="contactosContainer">
                                <!-- Contactos serão carregados aqui -->
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="adicionarContacto">
                                <i class="fas fa-plus me-2"></i>Adicionar Contacto
                            </button>
                            <div class="form-text">Adicione os contactos do centro (pelo menos um é recomendado)</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('centros.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Atualizar Centro
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
                        <i class="fas fa-info-circle me-2"></i>Informações
                    </h6>
                </div>
                <div class="card-body" id="infoCard">
                    <div class="text-center py-3">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p class="mb-0 mt-2">Carregando...</p>
                    </div>
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
    const centroId = {{ request()->route('id') ?? 'null' }};
    
    if (centroId) {
        carregarCentro(centroId);
    } else {
        Swal.fire('Erro!', 'ID do centro não fornecido.', 'error').then(() => {
            window.location.href = '{{ route("centros.index") }}';
        });
    }

    // Preview em tempo real
    $('#centroForm input, #centroForm select').on('input change', function() {
        atualizarPreview();
    });

    // Adicionar contacto
    $('#adicionarContacto').on('click', function() {
        adicionarContacto();
    });

    // Remover contacto (delegação de eventos)
    $(document).on('click', '.remover-contacto', function() {
        $(this).closest('.contacto-item').remove();
        verificarBotoesRemover();
        atualizarPreview();
    });

    // Submit do formulário
    $('#centroForm').on('submit', function(e) {
        e.preventDefault();
        atualizarCentro();
    });

    // Atualizar preview quando contactos mudarem
    $(document).on('input change', '.contacto-tipo, .contacto-valor', function() {
        atualizarPreview();
    });
});

function carregarCentro(id) {
    $.get(`/api/centros/${id}`)
        .done(function(centro) {
            // Preencher formulário
            $('#centro_id').val(centro.id);
            $('#nome').val(centro.nome);
            $('#localizacao').val(centro.localizacao);
            $('#email').val(centro.email || '');

            // Carregar contactos
            carregarContactos(centro.contactos);

            // Mostrar informações
            mostrarInformacoesCentro(centro);

            // Mostrar formulário
            $('#loadingContent').hide();
            $('#centroForm').show();

            // Atualizar preview inicial
            atualizarPreview();
        })
        .fail(function() {
            Swal.fire('Erro!', 'Centro não encontrado.', 'error').then(() => {
                window.location.href = '{{ route("centros.index") }}';
            });
        });
}

function carregarContactos(contactosData) {
    $('#contactosContainer').empty();
    
    try {
        const contactos = typeof contactosData === 'string' ? JSON.parse(contactosData) : contactosData;
        
        if (contactos && typeof contactos === 'object' && Object.keys(contactos).length > 0) {
            Object.entries(contactos).forEach(([tipo, valor]) => {
                adicionarContactoComDados(tipo, valor);
            });
        } else {
            // Se não há contactos, adicionar um item vazio
            adicionarContacto();
        }
    } catch (e) {
        // Se há erro no JSON, adicionar um item vazio
        adicionarContacto();
    }
    
    verificarBotoesRemover();
}

function adicionarContactoComDados(tipo = 'telefone', valor = '') {
    const novoContacto = `
        <div class="contacto-item row align-items-end mb-2">
            <div class="col-md-4">
                <label class="form-label small">Tipo de Contacto</label>
                <select class="form-select contacto-tipo" name="contacto_tipo[]">
                    <option value="telefone" ${tipo === 'telefone' ? 'selected' : ''}>Telefone</option>
                    <option value="telemovel" ${tipo === 'telemovel' ? 'selected' : ''}>Telemóvel</option>
                    <option value="fax" ${tipo === 'fax' ? 'selected' : ''}>Fax</option>
                    <option value="whatsapp" ${tipo === 'whatsapp' ? 'selected' : ''}>WhatsApp</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small">Valor</label>
                <input type="text" class="form-control contacto-valor" name="contacto_valor[]" value="${valor}" placeholder="Ex: +351 912 345 678">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm remover-contacto">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    $('#contactosContainer').append(novoContacto);
}

function adicionarContacto() {
    adicionarContactoComDados();
    verificarBotoesRemover();
}

function verificarBotoesRemover() {
    const totalContactos = $('.contacto-item').length;
    $('.remover-contacto').prop('disabled', totalContactos <= 1);
}

function mostrarInformacoesCentro(centro) {
    const dataFormatada = new Date(centro.created_at).toLocaleDateString('pt-PT');
    const dataAtualizacao = new Date(centro.updated_at).toLocaleDateString('pt-PT');

    const infoHtml = `
        <p><strong>ID:</strong> ${centro.id}</p>
        <p><strong>Data de Criação:</strong><br><small>${dataFormatada}</small></p>
        <p><strong>Última Atualização:</strong><br><small>${dataAtualizacao}</small></p>
        
        <hr>
        
        <h6 class="text-warning">Atenção:</h6>
        <ul class="small">
            <li>As alterações serão salvas imediatamente</li>
            <li>Certifique-se de que todos os dados estão corretos</li>
            <li>O nome do centro deve ser único no sistema</li>
        </ul>
    `;

    $('#infoCard').html(infoHtml);
}

function atualizarPreview() {
    const nome = $('#nome').val();
    const localizacao = $('#localizacao').val();
    const email = $('#email').val();

    if (nome || localizacao) {
        // Coletar contactos
        let contactosHtml = '';
        $('.contacto-item').each(function() {
            const tipo = $(this).find('.contacto-tipo').val();
            const valor = $(this).find('.contacto-valor').val();
            if (valor) {
                contactosHtml += `<small class="d-block"><strong>${tipo}:</strong> ${valor}</small>`;
            }
        });

        let preview = `
            <h6>${nome || 'Nome do Centro'}</h6>
            <p class="mb-1"><strong>Localização:</strong> ${localizacao || 'Não definida'}</p>
            <p class="mb-2"><strong>Email:</strong> ${email || 'Não definido'}</p>
            <div>
                <strong>Contactos:</strong>
                ${contactosHtml || '<small class="text-muted">Nenhum contacto adicionado</small>'}
            </div>
        `;

        $('#previewContent').html(preview);
        $('#previewCard').show();
    } else {
        $('#previewCard').hide();
    }
}

function atualizarCentro() {
    const centroId = $('#centro_id').val();
    
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
        nome: $('#nome').val(),
        localizacao: $('#localizacao').val(),
        email: $('#email').val() || null,
        contactos: contactos
    };

    $.ajax({
        url: `/api/centros/${centroId}`,
        method: 'PUT',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        beforeSend: function() {
            $('#centroForm button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Atualizando...');
        },
        success: function(response) {
            Swal.fire({
                title: 'Sucesso!',
                text: 'Centro atualizado com sucesso!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '{{ route("centros.index") }}';
            });
        },
        error: function(xhr) {
            let message = 'Ocorreu um erro ao atualizar o centro.';
            
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
            $('#centroForm button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Atualizar Centro');
        }
    });
}
</script>
@endsection
