@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-box me-3 text-primary"></i>Gestão de Produtos
                    </h1>
                    <p class="text-muted">Gerir todos os produtos disponíveis no sistema</p>
                </div>
                <a href="{{ route('produtos.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Novo Produto
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Lista de Produtos
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover data-table" id="produtosTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Status</th>
                            <th>Destaque</th>
                            <th>Imagem</th>
                            <th>Data Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="9" class="text-center">
                                <i class="fas fa-spinner fa-spin me-2"></i>Carregando produtos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Visualização -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Detalhes do Produto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewModalContent">
                <!-- Conteúdo será carregado via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    carregarProdutos();
});

function carregarProdutos() {
    $.get('/api/produtos?incluir_inativos=1', function(data) {
        let html = '';
        
        if (data.length === 0) {
            html = '<tr><td colspan="9" class="text-center text-muted">Nenhum produto encontrado</td></tr>';
        } else {
            data.forEach(function(produto) {
                const statusBadge = produto.ativo 
                    ? '<span class="badge bg-success">Ativo</span>' 
                    : '<span class="badge bg-secondary">Inativo</span>';
                
                const destaqueBadge = produto.em_destaque 
                    ? '<span class="badge bg-warning text-dark">Sim</span>' 
                    : '<span class="badge bg-light text-dark">Não</span>';
                
                const imagem = produto.imagem_url 
                    ? `<img src="${produto.imagem_url}" alt="Imagem do produto" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">` 
                    : '<span class="text-muted"><i class="fas fa-image"></i></span>';
                
                const preco = produto.preco ? `€${parseFloat(produto.preco).toFixed(2)}` : 'N/A';
                const dataFormatada = new Date(produto.created_at).toLocaleDateString('pt-PT');
                
                html += `
                    <tr>
                        <td>${produto.id}</td>
                        <td>
                            <strong>${produto.nome}</strong>
                            ${produto.descricao ? `<br><small class="text-muted">${produto.descricao.substring(0, 50)}...</small>` : ''}
                        </td>
                        <td>${produto.categoria ? produto.categoria.nome : 'Sem categoria'}</td>
                        <td><strong>${preco}</strong></td>
                        <td>${statusBadge}</td>
                        <td>${destaqueBadge}</td>
                        <td class="text-center">${imagem}</td>
                        <td>${dataFormatada}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="visualizarProduto(${produto.id})" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('produtos.edit', '') }}/${produto.id}" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarProduto(${produto.id})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        $('#produtosTable tbody').html(html);
        
        // Reinicializar DataTable se já existir
        if ($.fn.DataTable.isDataTable('#produtosTable')) {
            $('#produtosTable').DataTable().destroy();
        }
        
        $('#produtosTable').DataTable({
            language: window.dataTablesPortuguese,
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' + 
                 '<"row"<"col-sm-12"tr>>' + 
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            order: [[0, 'desc']]
        });
    });
}

function visualizarProduto(id) {
    $.get(`/api/produtos/${id}`, function(response) {
        // Se a resposta vem com status e dados, extrair dados
        const produto = response.dados || response;
        const statusBadge = produto.ativo 
            ? '<span class="badge bg-success">Ativo</span>' 
            : '<span class="badge bg-secondary">Inativo</span>';
        
        const destaqueBadge = produto.em_destaque 
            ? '<span class="badge bg-warning text-dark">Em Destaque</span>' 
            : '<span class="badge bg-light text-dark">Normal</span>';
        
        const imagem = produto.imagem_url 
            ? `<img src="${produto.imagem_url}" alt="Imagem do produto" class="img-fluid rounded" style="max-height: 200px;">` 
            : '<div class="alert alert-light text-center"><i class="fas fa-image fa-3x text-muted"></i><br>Sem imagem</div>';
        
        const preco = produto.preco ? `€${parseFloat(produto.preco).toFixed(2)}` : 'Preço não definido';
        
        let html = `
            <div class="row">
                <div class="col-md-4 text-center mb-3">
                    ${imagem}
                </div>
                <div class="col-md-8">
                    <h4>${produto.nome}</h4>
                    <p class="mb-2"><strong>Preço:</strong> <span class="h5 text-success">${preco}</span></p>
                    <p class="mb-2"><strong>Categoria:</strong> ${produto.categoria ? produto.categoria.nome : 'Sem categoria'}</p>
                    <p class="mb-2"><strong>Status:</strong> ${statusBadge}</p>
                    <p class="mb-2"><strong>Destaque:</strong> ${destaqueBadge}</p>
                    <p class="mb-2"><strong>Data de Criação:</strong> ${new Date(produto.created_at).toLocaleDateString('pt-PT')}</p>
                </div>
            </div>
            
            ${produto.descricao ? `
                <div class="mt-3">
                    <h6><strong>Descrição:</strong></h6>
                    <p class="text-muted">${produto.descricao}</p>
                </div>
            ` : ''}
        `;
        
        $('#viewModalContent').html(html);
        $('#viewModal').modal('show');
    });
}

function eliminarProduto(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Esta ação irá eliminar o produto permanentemente!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sim, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/produtos/${id}`,
                method: 'DELETE',
                success: function(response) {
                    Swal.fire(
                        'Eliminado!',
                        'O produto foi eliminado com sucesso.',
                        'success'
                    );
                    carregarProdutos();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Erro!',
                        'Ocorreu um erro ao eliminar o produto.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>
@endsection
