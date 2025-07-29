@extends('layouts.app')

@section('title', 'Pré-Inscrições')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-user-plus me-3 text-primary"></i>Gestão de Pré-Inscrições
                    </h1>
                    <p class="text-muted">Gerir todas as pré-inscrições dos candidatos</p>
                </div>
                <a href="{{ route('pre-inscricoes.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Nova Pré-Inscrição
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="filtroStatus" class="form-label">Status</label>
                    <select class="form-select" id="filtroStatus">
                        <option value="">Todos os status</option>
                        <option value="pendente">Pendente</option>
                        <option value="confirmado">Confirmado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroCurso" class="form-label">Curso</label>
                    <select class="form-select" id="filtroCurso">
                        <option value="">Todos os cursos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroCentro" class="form-label">Centro</label>
                    <select class="form-select" id="filtroCentro">
                        <option value="">Todos os centros</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary me-2" onclick="aplicarFiltros()">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="limparFiltros()">
                        <i class="fas fa-times me-2"></i>Limpar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Lista de Pré-Inscrições
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover data-table" id="preInscricoesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Curso</th>
                            <th>Centro</th>
                            <th>Horário</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="9" class="text-center">
                                <i class="fas fa-spinner fa-spin me-2"></i>Carregando pré-inscrições...
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
                    <i class="fas fa-eye me-2"></i>Detalhes da Pré-Inscrição
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
    carregarDados();
    carregarCursos();
    carregarCentros();
});

function carregarDados() {
    carregarPreInscricoes();
}

function carregarPreInscricoes() {
    const filtros = {
        status: $('#filtroStatus').val(),
        curso_id: $('#filtroCurso').val(),
        centro_id: $('#filtroCentro').val()
    };

    const queryString = Object.keys(filtros)
        .filter(key => filtros[key])
        .map(key => `${key}=${encodeURIComponent(filtros[key])}`)
        .join('&');

    const url = '/api/pre-inscricoes' + (queryString ? '?' + queryString : '');

    $.get(url, function(data) {
        let html = '';
        
        if (data.length === 0) {
            html = '<tr><td colspan="9" class="text-center text-muted">Nenhuma pré-inscrição encontrada</td></tr>';
        } else {
            data.forEach(function(preInscricao) {
                const statusBadge = getStatusBadge(preInscricao.status);
                const dataFormatada = new Date(preInscricao.created_at).toLocaleDateString('pt-PT');
                
                html += `
                    <tr>
                        <td>${preInscricao.id}</td>
                        <td>
                            <strong>${preInscricao.nome_completo}</strong>
                        </td>
                        <td>${preInscricao.email || '<span class="text-muted">N/A</span>'}</td>
                        <td>${preInscricao.curso ? preInscricao.curso.nome : '<span class="text-muted">N/A</span>'}</td>
                        <td>${preInscricao.centro ? preInscricao.centro.nome : '<span class="text-muted">N/A</span>'}</td>
                        <td>${preInscricao.horario ? preInscricao.horario.descricao : '<span class="text-muted">N/A</span>'}</td>
                        <td>${statusBadge}</td>
                        <td>${dataFormatada}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="visualizarPreInscricao(${preInscricao.id})" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('pre-inscricoes.edit', '') }}/${preInscricao.id}" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" title="Ações Rápidas">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        ${preInscricao.status === 'pendente' ? `
                                            <li><a class="dropdown-item" href="#" onclick="alterarStatus(${preInscricao.id}, 'confirmado')">
                                                <i class="fas fa-check me-2 text-success"></i>Confirmar
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="alterarStatus(${preInscricao.id}, 'cancelado')">
                                                <i class="fas fa-times me-2 text-danger"></i>Cancelar
                                            </a></li>
                                        ` : ''}
                                        ${preInscricao.status === 'confirmado' ? `
                                            <li><a class="dropdown-item" href="#" onclick="alterarStatus(${preInscricao.id}, 'cancelado')">
                                                <i class="fas fa-times me-2 text-danger"></i>Cancelar
                                            </a></li>
                                        ` : ''}
                                        ${preInscricao.status === 'cancelado' ? `
                                            <li><a class="dropdown-item" href="#" onclick="alterarStatus(${preInscricao.id}, 'pendente')">
                                                <i class="fas fa-undo me-2 text-warning"></i>Pendente
                                            </a></li>
                                        ` : ''}
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="eliminarPreInscricao(${preInscricao.id})">
                                            <i class="fas fa-trash me-2"></i>Eliminar
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        $('#preInscricoesTable tbody').html(html);
        
        // Reinicializar DataTable se já existir
        if ($.fn.DataTable.isDataTable('#preInscricoesTable')) {
            $('#preInscricoesTable').DataTable().destroy();
        }
        
        $('#preInscricoesTable').DataTable({
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

function carregarCursos() {
    $.get('/api/cursos', function(data) {
        let options = '<option value="">Todos os cursos</option>';
        data.forEach(function(curso) {
            options += `<option value="${curso.id}">${curso.nome}</option>`;
        });
        $('#filtroCurso').html(options);
    });
}

function carregarCentros() {
    $.get('/api/centros', function(data) {
        let options = '<option value="">Todos os centros</option>';
        data.forEach(function(centro) {
            options += `<option value="${centro.id}">${centro.nome}</option>`;
        });
        $('#filtroCentro').html(options);
    });
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

function aplicarFiltros() {
    carregarPreInscricoes();
}

function limparFiltros() {
    $('#filtroStatus').val('');
    $('#filtroCurso').val('');
    $('#filtroCentro').val('');
    carregarPreInscricoes();
}

function visualizarPreInscricao(id) {
    $.get(`/api/pre-inscricoes/${id}`, function(preInscricao) {
        const statusBadge = getStatusBadge(preInscricao.status);
        
        let contactos = '';
        if (preInscricao.contactos) {
            try {
                const contactosObj = typeof preInscricao.contactos === 'string' 
                    ? JSON.parse(preInscricao.contactos) 
                    : preInscricao.contactos;
                
                contactos = '<ul class="list-unstyled mb-0">';
                Object.keys(contactosObj).forEach(key => {
                    contactos += `<li><strong>${key}:</strong> ${contactosObj[key]}</li>`;
                });
                contactos += '</ul>';
            } catch (e) {
                contactos = preInscricao.contactos;
            }
        } else {
            contactos = '<span class="text-muted">Nenhum contacto registado</span>';
        }
        
        let html = `
            <div class="row">
                <div class="col-md-6">
                    <h5>${preInscricao.nome_completo}</h5>
                    <p class="mb-2"><strong>Email:</strong> ${preInscricao.email || '<span class="text-muted">N/A</span>'}</p>
                    <p class="mb-2"><strong>Status:</strong> ${statusBadge}</p>
                    <p class="mb-2"><strong>Data de Inscrição:</strong> ${new Date(preInscricao.created_at).toLocaleDateString('pt-PT')}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2"><strong>Curso:</strong> ${preInscricao.curso ? preInscricao.curso.nome : '<span class="text-muted">N/A</span>'}</p>
                    <p class="mb-2"><strong>Centro:</strong> ${preInscricao.centro ? preInscricao.centro.nome : '<span class="text-muted">N/A</span>'}</p>
                    <p class="mb-2"><strong>Horário:</strong> ${preInscricao.horario ? preInscricao.horario.descricao : '<span class="text-muted">N/A</span>'}</p>
                </div>
            </div>
            
            <div class="mt-3">
                <h6><strong>Contactos:</strong></h6>
                ${contactos}
            </div>
            
            ${preInscricao.observacoes ? `
                <div class="mt-3">
                    <h6><strong>Observações:</strong></h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">${preInscricao.observacoes}</p>
                    </div>
                </div>
            ` : ''}
        `;
        
        $('#viewModalContent').html(html);
        $('#viewModal').modal('show');
    });
}

function alterarStatus(id, novoStatus) {
    const statusTexto = {
        'pendente': 'pendente',
        'confirmado': 'confirmado',
        'cancelado': 'cancelado'
    };

    Swal.fire({
        title: 'Confirmar alteração',
        text: `Alterar status para "${statusTexto[novoStatus]}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sim, alterar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/pre-inscricoes/${id}`,
                method: 'PUT',
                data: JSON.stringify({ status: novoStatus }),
                contentType: 'application/json',
                success: function(response) {
                    Swal.fire(
                        'Alterado!',
                        'Status alterado com sucesso.',
                        'success'
                    );
                    carregarPreInscricoes();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Erro!',
                        'Ocorreu um erro ao alterar o status.',
                        'error'
                    );
                }
            });
        }
    });
}

function eliminarPreInscricao(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Esta ação irá eliminar a pré-inscrição permanentemente!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sim, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/pre-inscricoes/${id}`,
                method: 'DELETE',
                success: function(response) {
                    Swal.fire(
                        'Eliminado!',
                        'A pré-inscrição foi eliminada com sucesso.',
                        'success'
                    );
                    carregarPreInscricoes();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Erro!',
                        'Ocorreu um erro ao eliminar a pré-inscrição.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>
@endsection
