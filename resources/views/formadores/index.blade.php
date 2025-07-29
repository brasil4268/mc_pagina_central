@extends('layouts.app')

@section('title', 'Formadores')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-chalkboard-teacher me-3 text-primary"></i>Gestão de Formadores
                    </h1>
                    <p class="text-muted">Gerir todos os formadores disponíveis no sistema</p>
                </div>
                <a href="{{ route('formadores.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Novo Formador
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Lista de Formadores
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover data-table" id="formadoresTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Especialidade</th>
                            <th>Contactos</th>
                            <th>Data Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">
                                <i class="fas fa-spinner fa-spin me-2"></i>Carregando formadores...
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
                    <i class="fas fa-eye me-2"></i>Detalhes do Formador
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
    carregarFormadores();
});

function carregarFormadores() {
    $.get('/api/formadores', function(data) {
        let html = '';
        
        if (data.length === 0) {
            html = '<tr><td colspan="8" class="text-center text-muted">Nenhum formador encontrado</td></tr>';
        } else {
            data.forEach(function(formador) {
                const foto = formador.foto_url 
                    ? `<img src="${formador.foto_url}" alt="Foto do formador" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">` 
                    : '<i class="fas fa-user-circle fa-2x text-muted"></i>';
                
                const email = formador.email 
                    ? `<a href="mailto:${formador.email}" class="text-decoration-none">${formador.email}</a>`
                    : '<span class="text-muted">Não informado</span>';
                
                const especialidade = formador.especialidade || '<span class="text-muted">Não informada</span>';
                
                let contactos = '<span class="text-muted">Nenhum</span>';
                if (formador.contactos && Array.isArray(formador.contactos) && formador.contactos.length > 0) {
                    const firstContact = formador.contactos[0];
                    contactos = `${firstContact.tipo}: ${firstContact.valor}`;
                    if (formador.contactos.length > 1) {
                        contactos += ` <span class="badge bg-secondary">+${formador.contactos.length - 1}</span>`;
                    }
                }
                
                const dataFormatada = new Date(formador.created_at).toLocaleDateString('pt-PT');
                
                html += `
                    <tr>
                        <td>${formador.id}</td>
                        <td class="text-center">${foto}</td>
                        <td>
                            <strong>${formador.nome}</strong>
                            ${formador.bio ? `<br><small class="text-muted">${formador.bio.substring(0, 50)}...</small>` : ''}
                        </td>
                        <td>${email}</td>
                        <td>${especialidade}</td>
                        <td><small>${contactos}</small></td>
                        <td>${dataFormatada}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="visualizarFormador(${formador.id})" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('formadores.edit', '') }}/${formador.id}" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarFormador(${formador.id})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        $('#formadoresTable tbody').html(html);
        
        // Reinicializar DataTable se já existir
        if ($.fn.DataTable.isDataTable('#formadoresTable')) {
            $('#formadoresTable').DataTable().destroy();
        }
        
        $('#formadoresTable').DataTable({
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

function visualizarFormador(id) {
    $.get(`/api/formadores/${id}`, function(formador) {
        const foto = formador.foto_url 
            ? `<img src="${formador.foto_url}" alt="Foto do formador" class="img-fluid rounded-circle" style="max-width: 150px; max-height: 150px; object-fit: cover;">` 
            : '<div class="text-center"><i class="fas fa-user-circle fa-5x text-muted"></i><br><span class="text-muted">Sem foto</span></div>';
        
        const email = formador.email 
            ? `<a href="mailto:${formador.email}" class="text-decoration-none">${formador.email}</a>`
            : '<span class="text-muted">Não informado</span>';
        
        let contactosHtml = '<span class="text-muted">Nenhum contacto registado</span>';
        if (formador.contactos && Array.isArray(formador.contactos) && formador.contactos.length > 0) {
            contactosHtml = '<ul class="list-unstyled mb-0">';
            formador.contactos.forEach(function(contacto) {
                let icon = 'fas fa-phone';
                if (contacto.tipo.toLowerCase().includes('email')) icon = 'fas fa-envelope';
                else if (contacto.tipo.toLowerCase().includes('whatsapp')) icon = 'fab fa-whatsapp';
                else if (contacto.tipo.toLowerCase().includes('linkedin')) icon = 'fab fa-linkedin';
                
                contactosHtml += `<li><i class="${icon} me-2"></i><strong>${contacto.tipo}:</strong> ${contacto.valor}</li>`;
            });
            contactosHtml += '</ul>';
        }
        
        let html = `
            <div class="row">
                <div class="col-md-3 text-center mb-3">
                    ${foto}
                </div>
                <div class="col-md-9">
                    <h4>${formador.nome}</h4>
                    <p class="mb-2"><strong>Email:</strong> ${email}</p>
                    <p class="mb-2"><strong>Especialidade:</strong> ${formador.especialidade || 'Não informada'}</p>
                    <p class="mb-2"><strong>Data de Criação:</strong> ${new Date(formador.created_at).toLocaleDateString('pt-PT')}</p>
                </div>
            </div>
            
            ${formador.bio ? `
                <div class="mt-3">
                    <h6><strong>Biografia:</strong></h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0" style="white-space: pre-wrap;">${formador.bio}</p>
                    </div>
                </div>
            ` : ''}
            
            <div class="mt-3">
                <h6><strong>Contactos:</strong></h6>
                <div class="bg-light p-3 rounded">
                    ${contactosHtml}
                </div>
            </div>
        `;
        
        $('#viewModalContent').html(html);
        $('#viewModal').modal('show');
    });
}

function eliminarFormador(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Esta ação irá eliminar o formador permanentemente!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sim, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/formadores/${id}`,
                method: 'DELETE',
                success: function(response) {
                    Swal.fire(
                        'Eliminado!',
                        'O formador foi eliminado com sucesso.',
                        'success'
                    );
                    carregarFormadores();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Erro!',
                        'Ocorreu um erro ao eliminar o formador.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>
@endsection
