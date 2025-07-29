@extends('layouts.app')

@section('title', 'Categorias')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-tags me-3 text-primary"></i>Gestão de Categorias
                    </h1>
                    <p class="text-muted">Gerir todas as categorias disponíveis no sistema</p>
                </div>
                <a href="{{ route('categorias.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Nova Categoria
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Lista de Categorias
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover data-table" id="categoriasTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Data Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center">
                                <i class="fas fa-spinner fa-spin me-2"></i>Carregando categorias...
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
                    <i class="fas fa-eye me-2"></i>Detalhes da Categoria
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
    carregarCategorias();
});

function carregarCategorias() {
    $.get('/api/categorias', function(data) {
        let html = '';
        
        if (data.length === 0) {
            html = '<tr><td colspan="6" class="text-center text-muted">Nenhuma categoria encontrada</td></tr>';
        } else {
            data.forEach(function(categoria) {
                const statusBadge = categoria.ativo 
                    ? '<span class="badge bg-success">Ativo</span>' 
                    : '<span class="badge bg-secondary">Inativo</span>';
                
                const tipoBadge = categoria.tipo === 'loja' 
                    ? '<span class="badge bg-info">Loja</span>' 
                    : '<span class="badge bg-warning text-dark">Snack</span>';
                
                const dataFormatada = new Date(categoria.created_at).toLocaleDateString('pt-PT');
                
                html += `
                    <tr>
                        <td>${categoria.id}</td>
                        <td>
                            <strong>${categoria.nome}</strong>
                            ${categoria.descricao ? `<br><small class="text-muted">${categoria.descricao.substring(0, 50)}...</small>` : ''}
                        </td>
                        <td>${tipoBadge}</td>
                        <td>${statusBadge}</td>
                        <td>${dataFormatada}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="visualizarCategoria(${categoria.id})" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('categorias.edit', '') }}/${categoria.id}" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarCategoria(${categoria.id})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        $('#categoriasTable tbody').html(html);
        
        // Reinicializar DataTable se já existir
        if ($.fn.DataTable.isDataTable('#categoriasTable')) {
            $('#categoriasTable').DataTable().destroy();
        }
        
        $('#categoriasTable').DataTable({
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

function visualizarCategoria(id) {
    $.get(`/api/categorias/${id}`, function(response) {
        // Se a resposta vem com status e dados, extrair dados
        const categoria = response.dados || response;
        const statusBadge = categoria.ativo 
            ? '<span class="badge bg-success">Ativo</span>' 
            : '<span class="badge bg-secondary">Inativo</span>';
        
        const tipoBadge = categoria.tipo === 'loja' 
            ? '<span class="badge bg-info">Loja</span>' 
            : '<span class="badge bg-warning text-dark">Snack</span>';
        
        let html = `
            <div class="row">
                <div class="col-12">
                    <h4>${categoria.nome}</h4>
                    <p class="mb-2"><strong>Tipo:</strong> ${tipoBadge}</p>
                    <p class="mb-2"><strong>Status:</strong> ${statusBadge}</p>
                    <p class="mb-2"><strong>Data de Criação:</strong> ${new Date(categoria.created_at).toLocaleDateString('pt-PT')}</p>
                </div>
            </div>
            
            ${categoria.descricao ? `
                <div class="mt-3">
                    <h6><strong>Descrição:</strong></h6>
                    <p class="text-muted">${categoria.descricao}</p>
                </div>
            ` : ''}
        `;
        
        $('#viewModalContent').html(html);
        $('#viewModal').modal('show');
    });
}

function eliminarCategoria(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Esta ação irá eliminar a categoria permanentemente!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sim, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/categorias/${id}`,
                method: 'DELETE',
                success: function(response) {
                    Swal.fire(
                        'Eliminado!',
                        'A categoria foi eliminada com sucesso.',
                        'success'
                    );
                    carregarCategorias();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Erro!',
                        'Ocorreu um erro ao eliminar a categoria.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>
@endsection
