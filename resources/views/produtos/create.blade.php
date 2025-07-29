@extends('layouts.app')

@section('title', 'Novo Produto')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-plus me-3 text-primary"></i>Novo Produto
                    </h1>
                    <p class="text-muted">Criar um novo produto no sistema</p>
                </div>
                <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
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
                        <i class="fas fa-box me-2"></i>Informações do Produto
                    </h5>
                </div>
                <div class="card-body">
                    <form id="produtoForm">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nome" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome" name="nome" required maxlength="100">
                                <div class="form-text">Máximo 100 caracteres</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="preco" class="form-label">Preço (€) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="preco" name="preco" step="0.01" min="0" required>
                                <div class="form-text">Ex: 12.50</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="categoria_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                                <select class="form-select" id="categoria_id" name="categoria_id" required>
                                    <option value="">Carregando categorias...</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="ativo" class="form-label">Status</label>
                                <select class="form-select" id="ativo" name="ativo">
                                    <option value="1" selected>Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="em_destaque" class="form-label">Em Destaque</label>
                                <select class="form-select" id="em_destaque" name="em_destaque">
                                    <option value="0" selected>Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="imagem_url" class="form-label">URL da Imagem</label>
                            <input type="url" class="form-control" id="imagem_url" name="imagem_url" maxlength="255">
                            <div class="form-text">URL opcional para a imagem do produto</div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="4" maxlength="1000"></textarea>
                            <div class="form-text">Descrição detalhada do produto (máximo 1000 caracteres)</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Produto
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
                    <h6>Dicas para criar um produto:</h6>
                    <ul class="small">
                        <li><strong>Nome:</strong> Use um nome claro e descritivo</li>
                        <li><strong>Preço:</strong> Defina um preço justo e competitivo</li>
                        <li><strong>Categoria:</strong> Escolha a categoria apropriada</li>
                        <li><strong>Descrição:</strong> Explique as características do produto</li>
                        <li><strong>Imagem:</strong> Use URLs de imagens válidas</li>
                        <li><strong>Destaque:</strong> Produtos em destaque aparecem em posição privilegiada</li>
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
    carregarCategorias();

    // Preview em tempo real
    $('#produtoForm input, #produtoForm select, #produtoForm textarea').on('input change', function() {
        atualizarPreview();
    });

    // Submit do formulário
    $('#produtoForm').on('submit', function(e) {
        e.preventDefault();
        criarProduto();
    });
});

function carregarCategorias() {
    $.get('/api/categorias', function(data) {
        let options = '<option value="">Selecione uma categoria</option>';
        
        // Filtrar apenas categorias ativas
        const categoriasAtivas = data.filter(categoria => categoria.ativo);
        
        categoriasAtivas.forEach(function(categoria) {
            options += `<option value="${categoria.id}">${categoria.nome} (${categoria.tipo})</option>`;
        });
        
        $('#categoria_id').html(options);
    }).fail(function() {
        $('#categoria_id').html('<option value="">Erro ao carregar categorias</option>');
    });
}

function atualizarPreview() {
    const nome = $('#nome').val();
    const preco = $('#preco').val();
    const categoria_id = $('#categoria_id').val();
    const categoria_nome = $('#categoria_id option:selected').text();
    const ativo = $('#ativo').val();
    const em_destaque = $('#em_destaque').val();
    const imagem_url = $('#imagem_url').val();
    const descricao = $('#descricao').val();

    if (nome || preco) {
        const statusBadge = ativo == '1' 
            ? '<span class="badge bg-success">Ativo</span>' 
            : '<span class="badge bg-secondary">Inativo</span>';
        
        const destaqueBadge = em_destaque == '1' 
            ? '<span class="badge bg-warning text-dark">Em Destaque</span>' 
            : '<span class="badge bg-light text-dark">Normal</span>';
        
        const imagem = imagem_url 
            ? `<img src="${imagem_url}" alt="Preview" class="img-fluid rounded mb-2" style="max-height: 100px;" onerror="this.style.display='none'">` 
            : '';

        const precoFormatado = preco ? `€${parseFloat(preco).toFixed(2)}` : 'Preço não definido';

        let preview = `
            <div class="text-center mb-2">${imagem}</div>
            <h6>${nome || 'Nome do Produto'}</h6>
            <p class="mb-1"><strong>Preço:</strong> <span class="text-success">${precoFormatado}</span></p>
            <p class="mb-1"><strong>Categoria:</strong> ${categoria_id ? categoria_nome.split(' (')[0] : 'Não selecionada'}</p>
            <p class="mb-2">
                ${statusBadge} ${destaqueBadge}
            </p>
            ${descricao ? `<p class="small text-muted">${descricao.substring(0, 100)}...</p>` : ''}
        `;

        $('#previewContent').html(preview);
        $('#previewCard').show();
    } else {
        $('#previewCard').hide();
    }
}

function criarProduto() {
    const formData = {
        nome: $('#nome').val(),
        preco: parseFloat($('#preco').val()),
        categoria_id: parseInt($('#categoria_id').val()),
        ativo: parseInt($('#ativo').val()),
        em_destaque: parseInt($('#em_destaque').val()),
        imagem_url: $('#imagem_url').val() || null,
        descricao: $('#descricao').val() || null
    };

    $.ajax({
        url: '/api/produtos',
        method: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        beforeSend: function() {
            $('#produtoForm button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        },
        success: function(response) {
            Swal.fire({
                title: 'Sucesso!',
                text: 'Produto criado com sucesso!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '{{ route("produtos.index") }}';
            });
        },
        error: function(xhr) {
            let message = 'Ocorreu um erro ao criar o produto.';
            
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
            $('#produtoForm button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Produto');
        }
    });
}
</script>
@endsection
