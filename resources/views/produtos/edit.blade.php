@extends('layouts.app')

@section('title', 'Editar Produto')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-edit me-3 text-primary"></i>Editar Produto
                    </h1>
                    <p class="text-muted">Atualizar informações do produto</p>
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
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>Informações do Produto
                    </h5>
                </div>
                <div class="card-body">
                    <div id="loadingContent" class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                        <p>Carregando dados do produto...</p>
                    </div>

                    <form id="produtoForm" style="display: none;">
                        <input type="hidden" id="produto_id" name="id">
                        
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
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="em_destaque" class="form-label">Em Destaque</label>
                                <select class="form-select" id="em_destaque" name="em_destaque">
                                    <option value="0">Não</option>
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
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Atualizar Produto
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
    const produtoId = {{ request()->route('id') ?? 'null' }};
    
    carregarCategorias();
    
    if (produtoId) {
        carregarProduto(produtoId);
    } else {
        Swal.fire('Erro!', 'ID do produto não fornecido.', 'error').then(() => {
            window.location.href = '{{ route("produtos.index") }}';
        });
    }

    // Preview em tempo real
    $('#produtoForm input, #produtoForm select, #produtoForm textarea').on('input change', function() {
        atualizarPreview();
    });

    // Submit do formulário
    $('#produtoForm').on('submit', function(e) {
        e.preventDefault();
        atualizarProduto();
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

function carregarProduto(id) {
    $.get(`/api/produtos/${id}`)
        .done(function(produto) {
            // Preencher formulário
            $('#produto_id').val(produto.id);
            $('#nome').val(produto.nome);
            $('#preco').val(produto.preco);
            $('#categoria_id').val(produto.categoria_id);
            $('#ativo').val(produto.ativo ? 1 : 0);
            $('#em_destaque').val(produto.em_destaque ? 1 : 0);
            $('#imagem_url').val(produto.imagem_url || '');
            $('#descricao').val(produto.descricao || '');

            // Mostrar informações
            mostrarInformacoesProduto(produto);

            // Mostrar formulário
            $('#loadingContent').hide();
            $('#produtoForm').show();

            // Atualizar preview inicial
            atualizarPreview();
        })
        .fail(function() {
            Swal.fire('Erro!', 'Produto não encontrado.', 'error').then(() => {
                window.location.href = '{{ route("produtos.index") }}';
            });
        });
}

function mostrarInformacoesProduto(produto) {
    const dataFormatada = new Date(produto.created_at).toLocaleDateString('pt-PT');
    const dataAtualizacao = new Date(produto.updated_at).toLocaleDateString('pt-PT');

    const infoHtml = `
        <p><strong>ID:</strong> ${produto.id}</p>
        <p><strong>Data de Criação:</strong><br><small>${dataFormatada}</small></p>
        <p><strong>Última Atualização:</strong><br><small>${dataAtualizacao}</small></p>
        ${produto.categoria ? `<p><strong>Categoria Atual:</strong><br><small>${produto.categoria.nome} (${produto.categoria.tipo})</small></p>` : ''}
        
        <hr>
        
        <h6 class="text-warning">Atenção:</h6>
        <ul class="small">
            <li>As alterações serão salvas imediatamente</li>
            <li>Certifique-se de que todos os dados estão corretos</li>
            <li>Produtos inativos não aparecerão nas listagens públicas</li>
            <li>Produtos em destaque aparecem em posição privilegiada</li>
        </ul>
    `;

    $('#infoCard').html(infoHtml);
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

function atualizarProduto() {
    const produtoId = $('#produto_id').val();
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
        url: `/api/produtos/${produtoId}`,
        method: 'PUT',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        beforeSend: function() {
            $('#produtoForm button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Atualizando...');
        },
        success: function(response) {
            Swal.fire({
                title: 'Sucesso!',
                text: 'Produto atualizado com sucesso!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '{{ route("produtos.index") }}';
            });
        },
        error: function(xhr) {
            let message = 'Ocorreu um erro ao atualizar o produto.';
            
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
            $('#produtoForm button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Atualizar Produto');
        }
    });
}
</script>
@endsection
