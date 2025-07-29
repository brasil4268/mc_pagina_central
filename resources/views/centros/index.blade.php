@extends('layouts.app')

@section('title', 'Centros')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-building me-3 text-primary"></i>Gestão de Centros
                    </h1>
                    <p class="text-muted">Gerir todos os centros de formação no sistema</p>
                </div>
                <a href="{{ route('centros.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Novo Centro
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Lista de Centros
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover data-table" id="centrosTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Localização</th>
                            <th>Email</th>
                            <th>Contactos</th>
                            <th>Data Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center">
                                <i class="fas fa-spinner fa-spin me-2"></i>Carregando centros...
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
                    <i class="fas fa-eye me-2"></i>Detalhes do Centro
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
    carregarCentros();
});

function carregarCentros() {
    $.get('/api/centros', function(data) {
        let html = '';
        
        if (data.length === 0) {
            html = '<tr><td colspan="7" class="text-center text-muted">Nenhum centro encontrado</td></tr>';
        } else {
            data.forEach(function(centro) {
                let contactos = '';
                try {
                    const contactosObj = typeof centro.contactos === 'string' ? JSON.parse(centro.contactos) : centro.contactos;
                    if (contactosObj && typeof contactosObj === 'object') {
                        const contactosList = Object.entries(contactosObj).map(([tipo, valor]) => 
                            `<small class="d-block"><strong>${tipo}:</strong> ${valor}</small>`
                        ).join('');
                        contactos = contactosList || '<small class="text-muted">Não definido</small>';
                    } else {
                        contactos = '<small class="text-muted">Não definido</small>';
                    }
                } catch (e) {
                    contactos = '<small class="text-muted">Formato inválido</small>';
                }
                
                const dataFormatada = new Date(centro.created_at).toLocaleDateString('pt-PT');
                
                html += `
                    <tr>
                        <td>${centro.id}</td>
                        <td>
                            <strong>${centro.nome}</strong>
                        </td>
                        <td>${centro.localizacao}</td>
                        <td>${centro.email || '<span class="text-muted">Não definido</span>'}</td>
                        <td>${contactos}</td>
                        <td>${dataFormatada}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="visualizarCentro(${centro.id})" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('centros.edit', '') }}/${centro.id}" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarCentro(${centro.id})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        $('#centrosTable tbody').html(html);
        
        // Reinicializar DataTable se já existir
        if ($.fn.DataTable.isDataTable('#centrosTable')) {
            $('#centrosTable').DataTable().destroy();
        }
        
        $('#centrosTable').DataTable({
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

function visualizarCentro(id) {
    $.get(`/api/centros/${id}`, function(centro) {
        let contactosHtml = '';
        try {
            const contactosObj = typeof centro.contactos === 'string' ? JSON.parse(centro.contactos) : centro.contactos;
            if (contactosObj && typeof contactosObj === 'object') {
                contactosHtml = Object.entries(contactosObj).map(([tipo, valor]) => 
                    `<p class="mb-1"><strong>${tipo}:</strong> ${valor}</p>`
                ).join('');
            } else {
                contactosHtml = '<p class="text-muted">Nenhum contacto definido</p>';
            }
        } catch (e) {
            contactosHtml = '<p class="text-muted">Formato de contactos inválido</p>';
        }
        
        let html = `
            <div class="row">
                <div class="col-md-6">
                    <h4>${centro.nome}</h4>
                    <p class="mb-2"><strong>Localização:</strong> ${centro.localizacao}</p>
                    <p class="mb-2"><strong>Email:</strong> ${centro.email || 'Não definido'}</p>
                    <p class="mb-2"><strong>Data de Criação:</strong> ${new Date(centro.created_at).toLocaleDateString('pt-PT')}</p>
                </div>
                <div class="col-md-6">
                    <h6><strong>Contactos:</strong></h6>
                    <div class="bg-light p-3 rounded">
                        ${contactosHtml}
                    </div>
                </div>
            </div>
        `;
        
        $('#viewModalContent').html(html);
        $('#viewModal').modal('show');
    });
}

function eliminarCentro(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Esta ação irá eliminar o centro permanentemente!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sim, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/centros/${id}`,
                method: 'DELETE',
                success: function(response) {
                    Swal.fire(
                        'Eliminado!',
                        'O centro foi eliminado com sucesso.',
                        'success'
                    );
                    carregarCentros();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Erro!',
                        'Ocorreu um erro ao eliminar o centro.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>
@endsection
