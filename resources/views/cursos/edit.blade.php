@extends('layouts.app')

@section('title', 'Editar Curso')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 mb-2">
                        <i class="fas fa-edit me-3 text-primary"></i>Editar Curso
                    </h1>
                    <p class="text-muted">Atualizar informações do curso</p>
                </div>
                <a href="{{ route('cursos.index') }}" class="btn btn-outline-secondary">
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
                        <i class="fas fa-book me-2"></i>Informações do Curso
                    </h5>
                </div>
                <div class="card-body">
                    <div id="loadingContent" class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                        <p>Carregando dados do curso...</p>
                    </div>

                    <form id="cursoForm" style="display: none;">
                        <input type="hidden" id="curso_id" name="id">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nome" class="form-label">Nome do Curso <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome" name="nome" required maxlength="100">
                                <div class="form-text">Máximo 100 caracteres</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="area" class="form-label">Área <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="area" name="area" required maxlength="100">
                                <div class="form-text">Ex: Informática, Gestão, etc.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="modalidade" class="form-label">Modalidade <span class="text-danger">*</span></label>
                                <select class="form-select" id="modalidade" name="modalidade" required>
                                    <option value="">Selecione a modalidade</option>
                                    <option value="presencial">Presencial</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="ativo" class="form-label">Status</label>
                                <select class="form-select" id="ativo" name="ativo">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="imagem_url" class="form-label">URL da Imagem</label>
                            <input type="url" class="form-control" id="imagem_url" name="imagem_url" maxlength="255">
                            <div class="form-text">URL opcional para a imagem do curso</div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="4" maxlength="1000"></textarea>
                            <div class="form-text">Descrição detalhada do curso (máximo 1000 caracteres)</div>
                        </div>

                        <div class="mb-3">
                            <label for="programa" class="form-label">Programa do Curso</label>
                            <textarea class="form-control" id="programa" name="programa" rows="8" maxlength="5000"></textarea>
                            <div class="form-text">Programa detalhado, módulos, objetivos, etc. (máximo 5000 caracteres)</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('cursos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Atualizar Curso
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
    const cursoId = {{ request()->route('id') ?? 'null' }};
    
    if (cursoId) {
        carregarCurso(cursoId);
    } else {
        Swal.fire('Erro!', 'ID do curso não fornecido.', 'error').then(() => {
            window.location.href = '{{ route("cursos.index") }}';
        });
    }

    // Preview em tempo real
    $('#cursoForm input, #cursoForm select, #cursoForm textarea').on('input change', function() {
        atualizarPreview();
    });

    // Submit do formulário
    $('#cursoForm').on('submit', function(e) {
        e.preventDefault();
        atualizarCurso();
    });
});

function carregarCurso(id) {
    $.get(`/api/cursos/${id}`)
        .done(function(curso) {
            // Preencher formulário
            $('#curso_id').val(curso.id);
            $('#nome').val(curso.nome);
            $('#area').val(curso.area);
            $('#modalidade').val(curso.modalidade);
            $('#ativo').val(curso.ativo ? 1 : 0);
            $('#imagem_url').val(curso.imagem_url || '');
            $('#descricao').val(curso.descricao || '');
            $('#programa').val(curso.programa || '');

            // Mostrar informações
            mostrarInformacoesCurso(curso);

            // Mostrar formulário
            $('#loadingContent').hide();
            $('#cursoForm').show();

            // Atualizar preview inicial
            atualizarPreview();
        })
        .fail(function() {
            Swal.fire('Erro!', 'Curso não encontrado.', 'error').then(() => {
                window.location.href = '{{ route("cursos.index") }}';
            });
        });
}

function mostrarInformacoesCurso(curso) {
    const dataFormatada = new Date(curso.created_at).toLocaleDateString('pt-PT');
    const dataAtualizacao = new Date(curso.updated_at).toLocaleDateString('pt-PT');

    const infoHtml = `
        <p><strong>ID:</strong> ${curso.id}</p>
        <p><strong>Data de Criação:</strong><br><small>${dataFormatada}</small></p>
        <p><strong>Última Atualização:</strong><br><small>${dataAtualizacao}</small></p>
        
        <hr>
        
        <h6 class="text-warning">Atenção:</h6>
        <ul class="small">
            <li>As alterações serão salvas imediatamente</li>
            <li>Certifique-se de que todos os dados estão corretos</li>
            <li>Cursos inativos não aparecerão nas listagens públicas</li>
        </ul>
    `;

    $('#infoCard').html(infoHtml);
}

function atualizarPreview() {
    const nome = $('#nome').val();
    const area = $('#area').val();
    const modalidade = $('#modalidade').val();
    const ativo = $('#ativo').val();
    const imagem_url = $('#imagem_url').val();
    const descricao = $('#descricao').val();

    if (nome || area || modalidade) {
        const statusBadge = ativo == '1' 
            ? '<span class="badge bg-success">Ativo</span>' 
            : '<span class="badge bg-secondary">Inativo</span>';
        
        const modalidadeBadge = modalidade === 'online' 
            ? '<span class="badge bg-info">Online</span>' 
            : modalidade === 'presencial' 
                ? '<span class="badge bg-warning text-dark">Presencial</span>' 
                : '';
        
        const imagem = imagem_url 
            ? `<img src="${imagem_url}" alt="Preview" class="img-fluid rounded mb-2" style="max-height: 100px;" onerror="this.style.display='none'">` 
            : '';

        let preview = `
            <div class="text-center mb-2">${imagem}</div>
            <h6>${nome || 'Nome do Curso'}</h6>
            <p class="mb-1"><strong>Área:</strong> ${area || 'Não definida'}</p>
            <p class="mb-2">
                ${modalidadeBadge} ${statusBadge}
            </p>
            ${descricao ? `<p class="small text-muted">${descricao.substring(0, 100)}...</p>` : ''}
        `;

        $('#previewContent').html(preview);
        $('#previewCard').show();
    } else {
        $('#previewCard').hide();
    }
}

function atualizarCurso() {
    const cursoId = $('#curso_id').val();
    const formData = {
        nome: $('#nome').val(),
        area: $('#area').val(),
        modalidade: $('#modalidade').val(),
        ativo: parseInt($('#ativo').val()),
        imagem_url: $('#imagem_url').val() || null,
        descricao: $('#descricao').val() || null,
        programa: $('#programa').val() || null
    };

    $.ajax({
        url: `/api/cursos/${cursoId}`,
        method: 'PUT',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        beforeSend: function() {
            $('#cursoForm button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Atualizando...');
        },
        success: function(response) {
            Swal.fire({
                title: 'Sucesso!',
                text: 'Curso atualizado com sucesso!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '{{ route("cursos.index") }}';
            });
        },
        error: function(xhr) {
            let message = 'Ocorreu um erro ao atualizar o curso.';
            
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
            $('#cursoForm button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Atualizar Curso');
        }
    });
}
</script>
@endsection
