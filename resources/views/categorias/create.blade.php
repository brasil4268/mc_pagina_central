@extends('layouts.app')

@section('title', 'Nova Categoria')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-plus me-3 text-primary"></i>Nova Categoria
                    </h1>
                    <p class="text-muted">Criar uma nova categoria no sistema</p>
                </div>
                <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
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
                        <i class="fas fa-tags me-2"></i>Informações da Categoria
                    </h5>
                </div>
                <div class="card-body">
                    <form id="categoriaForm">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nome" class="form-label">Nome da Categoria <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome" name="nome" required maxlength="100">
                                <div class="form-text">Máximo 100 caracteres</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="loja">Loja</option>
                                    <option value="snack">Snack</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="ativo" class="form-label">Status</label>
                                <select class="form-select" id="ativo" name="ativo">
                                    <option value="1" selected>Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="4" maxlength="1000"></textarea>
                            <div class="form-text">Descrição detalhada da categoria (máximo 1000 caracteres)</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Categoria
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
                    <h6>Dicas para criar uma categoria:</h6>
                    <ul class="small">
                        <li><strong>Nome:</strong> Use um nome claro e descritivo</li>
                        <li><strong>Tipo:</strong> Loja para produtos vendidos na loja física, Snack para produtos do snack bar</li>
                        <li><strong>Descrição:</strong> Explique que tipo de produtos pertencem a esta categoria</li>
                        <li><strong>Status:</strong> Categorias inativas não aparecerão nas listagens públicas</li>
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
    // Preview em tempo real
    $('#categoriaForm input, #categoriaForm select, #categoriaForm textarea').on('input change', function() {
        atualizarPreview();
    });

    // Submit do formulário
    $('#categoriaForm').on('submit', function(e) {
        e.preventDefault();
        criarCategoria();
    });
});

function atualizarPreview() {
    const nome = $('#nome').val();
    const tipo = $('#tipo').val();
    const ativo = $('#ativo').val();
    const descricao = $('#descricao').val();

    if (nome || tipo) {
        const statusBadge = ativo == '1' 
            ? '<span class="badge bg-success">Ativo</span>' 
            : '<span class="badge bg-secondary">Inativo</span>';
        
        const tipoBadge = tipo === 'loja' 
            ? '<span class="badge bg-info">Loja</span>' 
            : tipo === 'snack' 
                ? '<span class="badge bg-warning text-dark">Snack</span>' 
                : '';

        let preview = `
            <h6>${nome || 'Nome da Categoria'}</h6>
            <p class="mb-2">
                ${tipoBadge} ${statusBadge}
            </p>
            ${descricao ? `<p class="small text-muted">${descricao.substring(0, 100)}...</p>` : ''}
        `;

        $('#previewContent').html(preview);
        $('#previewCard').show();
    } else {
        $('#previewCard').hide();
    }
}

function criarCategoria() {
    const formData = {
        nome: $('#nome').val(),
        tipo: $('#tipo').val(),
        ativo: parseInt($('#ativo').val()),
        descricao: $('#descricao').val() || null
    };

    $.ajax({
        url: '/api/categorias',
        method: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        beforeSend: function() {
            $('#categoriaForm button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        },
        success: function(response) {
            Swal.fire({
                title: 'Sucesso!',
                text: 'Categoria criada com sucesso!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '{{ route("categorias.index") }}';
            });
        },
        error: function(xhr) {
            let message = 'Ocorreu um erro ao criar a categoria.';
            
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
            $('#categoriaForm button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Categoria');
        }
    });
}
</script>
@endsection
