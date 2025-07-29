@extends('layouts.app')

@section('title', 'Horários')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-clock me-3 text-primary"></i>Gestão de Horários
                    </h1>
                    <p class="text-muted">Gerir todos os horários dos cursos no sistema</p>
                </div>
                <a href="{{ route('horarios.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Novo Horário
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Lista de Horários
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover data-table" id="horariosTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Curso</th>
                            <th>Centro</th>
                            <th>Dia da Semana</th>
                            <th>Período</th>
                            <th>Hora Início</th>
                            <th>Hora Fim</th>
                            <th>Data Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="9" class="text-center">
                                <i class="fas fa-spinner fa-spin me-2"></i>Carregando horários...
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
                    <i class="fas fa-eye me-2"></i>Detalhes do Horário
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
    carregarHorarios();
});

function carregarHorarios() {
    $.get('/api/horarios', function(data) {
        let html = '';
        
        if (data.length === 0) {
            html = '<tr><td colspan="9" class="text-center text-muted">Nenhum horário encontrado</td></tr>';
        } else {
            data.forEach(function(horario) {
                const periodoBadge = getPeriodoBadge(horario.periodo);
                const diaSemana = getDiaSemanaFormatado(horario.dia_semana);
                
                const horaInicio = horario.hora_inicio ? horario.hora_inicio.substring(0, 5) : '-';
                const horaFim = horario.hora_fim ? horario.hora_fim.substring(0, 5) : '-';
                
                const dataFormatada = new Date(horario.created_at).toLocaleDateString('pt-PT');
                
                html += `
                    <tr>
                        <td>${horario.id}</td>
                        <td>
                            <strong>${horario.curso ? horario.curso.nome : 'N/A'}</strong>
                            ${horario.curso && horario.curso.area ? `<br><small class="text-muted">${horario.curso.area}</small>` : ''}
                        </td>
                        <td>
                            <strong>${horario.centro ? horario.centro.nome : 'N/A'}</strong>
                            ${horario.centro && horario.centro.distrito ? `<br><small class="text-muted">${horario.centro.distrito}</small>` : ''}
                        </td>
                        <td>${diaSemana}</td>
                        <td>${periodoBadge}</td>
                        <td class="text-center">${horaInicio}</td>
                        <td class="text-center">${horaFim}</td>
                        <td>${dataFormatada}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="visualizarHorario(${horario.id})" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('horarios.edit', '') }}/${horario.id}" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarHorario(${horario.id})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        $('#horariosTable tbody').html(html);
        
        // Reinicializar DataTable se já existir
        if ($.fn.DataTable.isDataTable('#horariosTable')) {
            $('#horariosTable').DataTable().destroy();
        }
        
        $('#horariosTable').DataTable({
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

function getPeriodoBadge(periodo) {
    switch(periodo) {
        case 'manhã':
            return '<span class="badge bg-warning text-dark"><i class="fas fa-sun me-1"></i>Manhã</span>';
        case 'tarde':
            return '<span class="badge bg-primary"><i class="fas fa-cloud-sun me-1"></i>Tarde</span>';
        case 'noite':
            return '<span class="badge bg-dark"><i class="fas fa-moon me-1"></i>Noite</span>';
        default:
            return '<span class="badge bg-secondary">N/A</span>';
    }
}

function getDiaSemanaFormatado(dia) {
    const dias = {
        'Segunda': 'Segunda-feira',
        'Terça': 'Terça-feira',
        'Quarta': 'Quarta-feira',
        'Quinta': 'Quinta-feira',
        'Sexta': 'Sexta-feira',
        'Sábado': 'Sábado',
        'Domingo': 'Domingo'
    };
    return dias[dia] || dia;
}

function visualizarHorario(id) {
    $.get(`/api/horarios/${id}`, function(horario) {
        const periodoBadge = getPeriodoBadge(horario.periodo);
        const diaSemana = getDiaSemanaFormatado(horario.dia_semana);
        
        const horaInicio = horario.hora_inicio ? horario.hora_inicio.substring(0, 5) : 'Não definida';
        const horaFim = horario.hora_fim ? horario.hora_fim.substring(0, 5) : 'Não definida';
        
        let html = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h5><i class="fas fa-book me-2 text-primary"></i>Curso</h5>
                    <div class="p-3 bg-light rounded">
                        <h6>${horario.curso ? horario.curso.nome : 'N/A'}</h6>
                        ${horario.curso && horario.curso.area ? `<p class="mb-1"><strong>Área:</strong> ${horario.curso.area}</p>` : ''}
                        ${horario.curso && horario.curso.modalidade ? `<p class="mb-0"><strong>Modalidade:</strong> ${horario.curso.modalidade}</p>` : ''}
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <h5><i class="fas fa-building me-2 text-info"></i>Centro</h5>
                    <div class="p-3 bg-light rounded">
                        <h6>${horario.centro ? horario.centro.nome : 'N/A'}</h6>
                        ${horario.centro && horario.centro.distrito ? `<p class="mb-1"><strong>Distrito:</strong> ${horario.centro.distrito}</p>` : ''}
                        ${horario.centro && horario.centro.endereco ? `<p class="mb-0"><strong>Endereço:</strong> ${horario.centro.endereco}</p>` : ''}
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h6><i class="fas fa-calendar-day me-2"></i>Dia da Semana</h6>
                    <p class="h5">${diaSemana}</p>
                </div>
                
                <div class="col-md-4 mb-3">
                    <h6><i class="fas fa-clock me-2"></i>Período</h6>
                    <p>${periodoBadge}</p>
                </div>
                
                <div class="col-md-4 mb-3">
                    <h6><i class="fas fa-stopwatch me-2"></i>Horários</h6>
                    <p class="mb-1"><strong>Início:</strong> ${horaInicio}</p>
                    <p class="mb-0"><strong>Fim:</strong> ${horaFim}</p>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <h6><i class="fas fa-info-circle me-2"></i>Informações Adicionais</h6>
                    <p class="mb-1"><strong>Data de Criação:</strong> ${new Date(horario.created_at).toLocaleDateString('pt-PT')}</p>
                    <p class="mb-0"><strong>Última Atualização:</strong> ${new Date(horario.updated_at).toLocaleDateString('pt-PT')}</p>
                </div>
            </div>
        `;
        
        $('#viewModalContent').html(html);
        $('#viewModal').modal('show');
    });
}

function eliminarHorario(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Esta ação irá eliminar o horário permanentemente!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sim, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/horarios/${id}`,
                method: 'DELETE',
                success: function(response) {
                    Swal.fire(
                        'Eliminado!',
                        'O horário foi eliminado com sucesso.',
                        'success'
                    );
                    carregarHorarios();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Erro!',
                        'Ocorreu um erro ao eliminar o horário.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>
@endsection
