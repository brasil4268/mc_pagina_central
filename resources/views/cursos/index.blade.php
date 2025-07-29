@extends('layouts.app')

@section('title', 'Cursos')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-book me-3 text-primary"></i>Gestão de Cursos
                    </h1>
                    <p class="text-muted">Gerir todos os cursos disponíveis no sistema</p>
                </div>
                <a href="{{ route('cursos.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Novo Curso
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Lista de Cursos
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover data-table" id="cursosTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Área</th>
                            <th>Modalidade</th>
                            <th>Status</th>
                            <th>Imagem</th>
                            <th>Data Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">
                                <i class="fas fa-spinner fa-spin me-2"></i>Carregando cursos...
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
                    <i class="fas fa-eye me-2"></i>Detalhes do Curso
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
    carregarCursos();
});

function carregarCursos() {
    $.get('/api/cursos', function(data) {
        let html = '';
        
        if (data.length === 0) {
            html = '<tr><td colspan="8" class="text-center text-muted">Nenhum curso encontrado</td></tr>';
        } else {
            data.forEach(function(curso) {
                const statusBadge = curso.ativo 
                    ? '<span class="badge bg-success">Ativo</span>' 
                    : '<span class="badge bg-secondary">Inativo</span>';
                
                const modalidadeBadge = curso.modalidade === 'online' 
                    ? '<span class="badge bg-info">Online</span>' 
                    : '<span class="badge bg-warning text-dark">Presencial</span>';
                
                const imagem = curso.imagem_url 
                    ? `<img src="${curso.imagem_url}" alt="Imagem do curso" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">` 
                    : '<span class="text-muted"><i class="fas fa-image"></i></span>';
                
                const dataFormatada = new Date(curso.created_at).toLocaleDateString('pt-PT');
                
                html += `
                    <tr>
                        <td>${curso.id}</td>
                        <td>
                            <strong>${curso.nome}</strong>
                            ${curso.descricao ? `<br><small class="text-muted">${curso.descricao.substring(0, 50)}...</small>` : ''}
                        </td>
                        <td>${curso.area}</td>
                        <td>${modalidadeBadge}</td>
                        <td>${statusBadge}</td>
                        <td class="text-center">${imagem}</td>
                        <td>${dataFormatada}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="visualizarCurso(${curso.id})" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('cursos.edit', '') }}/${curso.id}" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarCurso(${curso.id})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        $('#cursosTable tbody').html(html);
        
        // Reinicializar DataTable se já existir
        if ($.fn.DataTable.isDataTable('#cursosTable')) {
            $('#cursosTable').DataTable().destroy();
        }
        
        $('#cursosTable').DataTable({
            language: window.dataTablesPortuguese,
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']],
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
        });
    });
}

function visualizarCurso(id) {
    $.get(`/api/cursos/${id}`, function(response) {
        // Se a resposta vem com status e dados, extrair dados
        const curso = response.dados || response;
        const statusBadge = curso.ativo 
            ? '<span class="badge bg-success">Ativo</span>' 
            : '<span class="badge bg-secondary">Inativo</span>';
        
        const modalidadeBadge = curso.modalidade === 'online' 
            ? '<span class="badge bg-info">Online</span>' 
            : '<span class="badge bg-warning text-dark">Presencial</span>';
        
        const imagem = curso.imagem_url 
            ? `<img src="${curso.imagem_url}" alt="Imagem do curso" class="img-fluid rounded" style="max-height: 200px;">` 
            : '<div class="alert alert-light text-center"><i class="fas fa-image fa-3x text-muted"></i><br>Sem imagem</div>';
        
        let html = `
            <div class="row">
                <div class="col-md-4 text-center mb-3">
                    ${imagem}
                </div>
                <div class="col-md-8">
                    <h4>${curso.nome}</h4>
                    <p class="mb-2"><strong>Área:</strong> ${curso.area}</p>
                    <p class="mb-2"><strong>Modalidade:</strong> ${modalidadeBadge}</p>
                    <p class="mb-2"><strong>Status:</strong> ${statusBadge}</p>
                    <p class="mb-2"><strong>Data de Criação:</strong> ${new Date(curso.created_at).toLocaleDateString('pt-PT')}</p>
                </div>
            </div>
            
            ${curso.descricao ? `
                <div class="mt-3">
                    <h6><strong>Descrição:</strong></h6>
                    <p class="text-muted">${curso.descricao}</p>
                </div>
            ` : ''}
            
            ${curso.programa ? `
                <div class="mt-3">
                    <h6><strong>Programa:</strong></h6>
                    <div class="bg-light p-3 rounded">
                        <pre class="mb-0" style="white-space: pre-wrap;">${curso.programa}</pre>
                    </div>
                </div>
            ` : ''}
        `;
        
        $('#viewModalContent').html(html);
        $('#viewModal').modal('show');
    });
}

function eliminarCurso(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Esta ação irá eliminar o curso permanentemente!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sim, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/cursos/${id}`,
                method: 'DELETE',
                success: function(response) {
                    Swal.fire(
                        'Eliminado!',
                        'O curso foi eliminado com sucesso.',
                        'success'
                    );
                    carregarCursos();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Erro!',
                        'Ocorreu um erro ao eliminar o curso.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>
@endsection
