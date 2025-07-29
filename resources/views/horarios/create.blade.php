@extends('layouts.app')

@section('title', 'Novo Horário')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-plus me-3 text-primary"></i>Novo Horário
                    </h1>
                    <p class="text-muted">Criar um novo horário no sistema</p>
                </div>
                <a href="{{ route('horarios.index') }}" class="btn btn-outline-secondary">
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
                        <i class="fas fa-clock me-2"></i>Informações do Horário
                    </h5>
                </div>
                <div class="card-body">
                    <form id="horarioForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="curso_id" class="form-label">Curso <span class="text-danger">*</span></label>
                                <select class="form-select" id="curso_id" name="curso_id" required>
                                    <option value="">Selecione o curso</option>
                                </select>
                                <div class="form-text">Escolha o curso para este horário</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="centro_id" class="form-label">Centro <span class="text-danger">*</span></label>
                                <select class="form-select" id="centro_id" name="centro_id" required>
                                    <option value="">Selecione o centro</option>
                                </select>
                                <div class="form-text">Escolha o centro onde será ministrado</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="dia_semana" class="form-label">Dia da Semana <span class="text-danger">*</span></label>
                                <select class="form-select" id="dia_semana" name="dia_semana" required>
                                    <option value="">Selecione o dia</option>
                                    <option value="Segunda">Segunda-feira</option>
                                    <option value="Terça">Terça-feira</option>
                                    <option value="Quarta">Quarta-feira</option>
                                    <option value="Quinta">Quinta-feira</option>
                                    <option value="Sexta">Sexta-feira</option>
                                    <option value="Sábado">Sábado</option>
                                    <option value="Domingo">Domingo</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="periodo" class="form-label">Período <span class="text-danger">*</span></label>
                                <select class="form-select" id="periodo" name="periodo" required>
                                    <option value="">Selecione o período</option>
                                    <option value="manhã">Manhã</option>
                                    <option value="tarde">Tarde</option>
                                    <option value="noite">Noite</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hora_inicio" class="form-label">Hora de Início</label>
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio">
                                <div class="form-text">Opcional - Hora específica de início</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="hora_fim" class="form-label">Hora de Fim</label>
                                <input type="time" class="form-control" id="hora_fim" name="hora_fim">
                                <div class="form-text">Opcional - Hora específica de término</div>
                            </div>
                        </div>

                        <div class="alert alert-info" id="horarioAlert" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="horarioAlertMessage"></span>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('horarios.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Horário
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
                    <h6>Dicas para criar um horário:</h6>
                    <ul class="small">
                        <li><strong>Curso:</strong> Selecione o curso que será ministrado</li>
                        <li><strong>Centro:</strong> Escolha onde as aulas acontecerão</li>
                        <li><strong>Dia:</strong> Dia da semana das aulas</li>
                        <li><strong>Período:</strong> Manhã, tarde ou noite</li>
                        <li><strong>Horas:</strong> Opcional - especifique horários exatos</li>
                    </ul>
                    
                    <h6 class="mt-3">Períodos sugeridos:</h6>
                    <ul class="small">
                        <li><strong>Manhã:</strong> 08:00 - 12:00</li>
                        <li><strong>Tarde:</strong> 14:00 - 18:00</li>
                        <li><strong>Noite:</strong> 19:00 - 23:00</li>
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
    carregarCursos();
    carregarCentros();
    
    // Preview em tempo real
    $('#horarioForm select, #horarioForm input').on('change input', function() {
        atualizarPreview();
        validarHorarios();
    });

    // Submit do formulário
    $('#horarioForm').on('submit', function(e) {
        e.preventDefault();
        if (validarHorarios()) {
            criarHorario();
        }
    });
});

function carregarCursos() {
    $.get('/api/cursos', function(data) {
        let options = '<option value="">Selecione o curso</option>';
        data.forEach(function(curso) {
            if (curso.ativo) {
                options += `<option value="${curso.id}">${curso.nome} - ${curso.area}</option>`;
            }
        });
        $('#curso_id').html(options);
    });
}

function carregarCentros() {
    $.get('/api/centros', function(data) {
        let options = '<option value="">Selecione o centro</option>';
        data.forEach(function(centro) {
            if (centro.ativo) {
                options += `<option value="${centro.id}">${centro.nome} - ${centro.distrito}</option>`;
            }
        });
        $('#centro_id').html(options);
    });
}

function validarHorarios() {
    const horaInicio = $('#hora_inicio').val();
    const horaFim = $('#hora_fim').val();
    
    $('#horarioAlert').hide();
    
    if (horaInicio && horaFim) {
        if (horaFim <= horaInicio) {
            $('#horarioAlertMessage').text('A hora de fim deve ser posterior à hora de início.');
            $('#horarioAlert').removeClass('alert-info').addClass('alert-warning').show();
            return false;
        }
        
        // Calcular duração
        const inicio = new Date(`2000-01-01 ${horaInicio}`);
        const fim = new Date(`2000-01-01 ${horaFim}`);
        const duracao = (fim - inicio) / (1000 * 60); // minutos
        
        if (duracao < 30) {
            $('#horarioAlertMessage').text('A duração mínima recomendada é de 30 minutos.');
            $('#horarioAlert').removeClass('alert-warning').addClass('alert-info').show();
        } else if (duracao > 480) { // 8 horas
            $('#horarioAlertMessage').text('A duração é superior a 8 horas. Verifique se está correto.');
            $('#horarioAlert').removeClass('alert-warning').addClass('alert-info').show();
        }
    }
    
    return true;
}

function atualizarPreview() {
    const cursoId = $('#curso_id').val();
    const centroId = $('#centro_id').val();
    const diaSemana = $('#dia_semana').val();
    const periodo = $('#periodo').val();
    const horaInicio = $('#hora_inicio').val();
    const horaFim = $('#hora_fim').val();

    if (cursoId || centroId || diaSemana || periodo) {
        const cursoNome = $('#curso_id option:selected').text();
        const centroNome = $('#centro_id option:selected').text();
        
        const periodoBadge = getPeriodoBadge(periodo);
        const diaSemanaFormatado = getDiaSemanaFormatado(diaSemana);
        
        const horarios = (horaInicio && horaFim) 
            ? `<p class="mb-1"><i class="fas fa-clock me-1"></i> ${horaInicio} - ${horaFim}</p>`
            : '';

        let preview = `
            <div class="text-center mb-3">
                <h6><i class="fas fa-calendar-alt me-2"></i>Horário</h6>
            </div>
            
            ${cursoId ? `<p class="mb-2"><strong><i class="fas fa-book me-1"></i> Curso:</strong><br>${cursoNome}</p>` : ''}
            ${centroId ? `<p class="mb-2"><strong><i class="fas fa-building me-1"></i> Centro:</strong><br>${centroNome}</p>` : ''}
            ${diaSemana ? `<p class="mb-2"><strong><i class="fas fa-calendar-day me-1"></i> Dia:</strong><br>${diaSemanaFormatado}</p>` : ''}
            ${periodo ? `<p class="mb-2"><strong><i class="fas fa-sun me-1"></i> Período:</strong><br>${periodoBadge}</p>` : ''}
            ${horarios}
        `;

        $('#previewContent').html(preview);
        $('#previewCard').show();
    } else {
        $('#previewCard').hide();
    }
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
            return '';
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

function criarHorario() {
    const formData = {
        curso_id: parseInt($('#curso_id').val()),
        centro_id: parseInt($('#centro_id').val()),
        dia_semana: $('#dia_semana').val(),
        periodo: $('#periodo').val(),
        hora_inicio: $('#hora_inicio').val() || null,
        hora_fim: $('#hora_fim').val() || null
    };

    $.ajax({
        url: '/api/horarios',
        method: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        beforeSend: function() {
            $('#horarioForm button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        },
        success: function(response) {
            Swal.fire({
                title: 'Sucesso!',
                text: 'Horário criado com sucesso!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '{{ route("horarios.index") }}';
            });
        },
        error: function(xhr) {
            let message = 'Ocorreu um erro ao criar o horário.';
            
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
            $('#horarioForm button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Horário');
        }
    });
}
</script>
@endsection
