<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MC Formação - Pré-Inscrição</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #64748b;
            --success-color: #16a34a;
            --light-color: #f8fafc;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .hero-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            margin: 2rem 0;
        }
        
        .hero-header {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            padding: 2rem;
            border-radius: 1rem 1rem 0 0;
            text-align: center;
        }
        
        .course-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
        }
        
        .btn {
            border-radius: 0.5rem;
            font-weight: 600;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            border: none;
        }
        
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
        }
        
        .contacto-group {
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 1rem;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .step.active {
            background: var(--primary-color);
            color: white;
        }
        
        .step.completed {
            background: var(--success-color);
            color: white;
        }
        
        .step-line {
            width: 60px;
            height: 2px;
            background: #e2e8f0;
            align-self: center;
        }
        
        .step-line.completed {
            background: var(--success-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero-section">
            <div class="hero-header">
                <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                <h1 class="display-5 fw-bold">MC Formação</h1>
                <p class="lead mb-0">Inscreva-se nos nossos cursos de formação</p>
            </div>
            
            <div class="p-4">
                <!-- Indicador de Passos -->
                <div class="step-indicator">
                    <div class="step active" id="step1">1</div>
                    <div class="step-line" id="line1"></div>
                    <div class="step" id="step2">2</div>
                    <div class="step-line" id="line2"></div>
                    <div class="step" id="step3">3</div>
                </div>

                <!-- Passo 1: Escolher Curso -->
                <div id="passo1" class="step-content">
                    <h3 class="text-center mb-4">
                        <i class="fas fa-search me-2 text-primary"></i>Escolha o seu curso
                    </h3>
                    
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Filtrar por área:</label>
                            <select class="form-select" id="filtroArea">
                                <option value="">Todas as áreas</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Filtrar por modalidade:</label>
                            <select class="form-select" id="filtroModalidade">
                                <option value="">Todas as modalidades</option>
                                <option value="presencial">Presencial</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                    </div>

                    <div id="cursosContainer" class="row">
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                            <p>Carregando cursos disponíveis...</p>
                        </div>
                    </div>
                </div>

                <!-- Passo 2: Escolher Centro e Horário -->
                <div id="passo2" class="step-content" style="display: none;">
                    <h3 class="text-center mb-4">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>Escolha o centro e horário
                    </h3>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="centro_id" class="form-label">Centro de Formação <span class="text-danger">*</span></label>
                            <select class="form-select" id="centro_id" required>
                                <option value="">Selecione um centro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="horario_id" class="form-label">Horário (opcional)</label>
                            <select class="form-select" id="horario_id">
                                <option value="">Selecione um horário</option>
                            </select>
                        </div>
                    </div>

                    <div id="centroInfo" class="alert alert-info" style="display: none;">
                        <!-- Informações do centro selecionado -->
                    </div>
                </div>

                <!-- Passo 3: Dados Pessoais -->
                <div id="passo3" class="step-content" style="display: none;">
                    <h3 class="text-center mb-4">
                        <i class="fas fa-user me-2 text-primary"></i>Os seus dados
                    </h3>
                    
                    <form id="inscricaoForm">
                        <input type="hidden" id="curso_id" name="curso_id">
                        <input type="hidden" id="selected_centro_id" name="centro_id">
                        <input type="hidden" id="selected_horario_id" name="horario_id">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nome_completo" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome_completo" name="nome_completo" required maxlength="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="100">
                            </div>
                        </div>

                        <!-- Contactos -->
                        <div class="mb-4">
                            <label class="form-label">Contactos <span class="text-danger">*</span></label>
                            <div id="contactosContainer">
                                <div class="contacto-group">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <select class="form-select contacto-tipo">
                                                <option value="telefone">Telefone</option>
                                                <option value="telemovel">Telemóvel</option>
                                                <option value="whatsapp">WhatsApp</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <input type="text" class="form-control contacto-valor" placeholder="Número de contacto" required>
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-contacto" style="display: none;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addContacto">
                                <i class="fas fa-plus me-1"></i>Adicionar Contacto
                            </button>
                        </div>

                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações (opcional)</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3" maxlength="500" placeholder="Alguma informação adicional que queira partilhar..."></textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Submeter Pré-Inscrição
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Botões de Navegação -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary" id="btnAnterior" style="display: none;">
                        <i class="fas fa-arrow-left me-2"></i>Anterior
                    </button>
                    <button type="button" class="btn btn-primary" id="btnProximo">
                        Próximo<i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Sucesso -->
    <div class="modal fade" id="sucessoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>Pré-Inscrição Enviada!
                    </h5>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5>Obrigado pela sua pré-inscrição!</h5>
                    <p class="text-muted">Recebemos a sua solicitação e entraremos em contacto em breve.</p>
                    <p><strong>Referência:</strong> <span id="referenciaInscricao"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="novaInscricao()">
                        <i class="fas fa-plus me-2"></i>Nova Pré-Inscrição
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let passoAtual = 1;
        let cursoSelecionado = null;
        let centroSelecionado = null;
        let cursos = [];
        let centros = [];
        let horarios = [];

        $(document).ready(function() {
            // Setup CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            carregarDados();
            configurarEventos();
        });

        function carregarDados() {
            // Carregar cursos
            $.get('/api/cursos', function(data) {
                cursos = data.filter(curso => curso.ativo);
                exibirCursos();
                preencherFiltroAreas();
            });

            // Carregar centros
            $.get('/api/centros', function(data) {
                centros = data;
                preencherSelectCentros();
            });

            // Carregar horários
            $.get('/api/horarios', function(data) {
                horarios = data;
            });
        }

        function preencherFiltroAreas() {
            const areas = [...new Set(cursos.map(curso => curso.area))];
            $('#filtroArea').html('<option value="">Todas as áreas</option>');
            areas.forEach(area => {
                $('#filtroArea').append(`<option value="${area}">${area}</option>`);
            });
        }

        function preencherSelectCentros() {
            $('#centro_id').html('<option value="">Selecione um centro</option>');
            centros.forEach(centro => {
                $('#centro_id').append(`<option value="${centro.id}">${centro.nome} - ${centro.localizacao}</option>`);
            });
        }

        function exibirCursos() {
            const areaFiltro = $('#filtroArea').val();
            const modalidadeFiltro = $('#filtroModalidade').val();
            
            let cursosFiltrados = cursos;
            
            if (areaFiltro) {
                cursosFiltrados = cursosFiltrados.filter(curso => curso.area === areaFiltro);
            }
            
            if (modalidadeFiltro) {
                cursosFiltrados = cursosFiltrados.filter(curso => curso.modalidade === modalidadeFiltro);
            }

            let html = '';
            
            if (cursosFiltrados.length === 0) {
                html = '<div class="col-12 text-center py-5"><i class="fas fa-search fa-2x mb-3 text-muted"></i><p>Nenhum curso encontrado com os filtros selecionados.</p></div>';
            } else {
                cursosFiltrados.forEach(curso => {
                    const modalidadeBadge = curso.modalidade === 'online' 
                        ? '<span class="badge bg-info">Online</span>' 
                        : '<span class="badge bg-warning text-dark">Presencial</span>';
                    
                    const imagem = curso.imagem_url 
                        ? `<img src="${curso.imagem_url}" alt="${curso.nome}" class="card-img-top" style="height: 200px; object-fit: cover;">` 
                        : '<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;"><i class="fas fa-book fa-3x text-muted"></i></div>';

                    html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="course-card card h-100" onclick="selecionarCurso(${curso.id})">
                                ${imagem}
                                <div class="card-body">
                                    <h5 class="card-title">${curso.nome}</h5>
                                    <p class="card-text">
                                        <strong>Área:</strong> ${curso.area}<br>
                                        ${modalidadeBadge}
                                    </p>
                                    ${curso.descricao ? `<p class="card-text text-muted small">${curso.descricao.substring(0, 100)}...</p>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            $('#cursosContainer').html(html);
        }

        function selecionarCurso(cursoId) {
            cursoSelecionado = cursos.find(c => c.id === cursoId);
            $('#curso_id').val(cursoId);
            
            // Destacar curso selecionado
            $('.course-card').removeClass('border-primary');
            $(event.currentTarget).addClass('border-primary');
            
            // Habilitar botão próximo
            $('#btnProximo').prop('disabled', false);
        }

        function configurarEventos() {
            // Filtros
            $('#filtroArea, #filtroModalidade').on('change', exibirCursos);

            // Navegação
            $('#btnProximo').on('click', proximoPasso);
            $('#btnAnterior').on('click', passoAnterior);

            // Centro selecionado
            $('#centro_id').on('change', function() {
                const centroId = $(this).val();
                if (centroId) {
                    centroSelecionado = centros.find(c => c.id == centroId);
                    $('#selected_centro_id').val(centroId);
                    mostrarInfoCentro();
                    carregarHorarios();
                }
            });

            // Contactos
            $('#addContacto').on('click', adicionarContacto);
            $(document).on('click', '.remove-contacto', function() {
                $(this).closest('.contacto-group').remove();
                atualizarBotoesRemover();
            });

            // Submit do formulário
            $('#inscricaoForm').on('submit', function(e) {
                e.preventDefault();
                submeterInscricao();
            });
        }

        function proximoPasso() {
            if (passoAtual === 1) {
                if (!cursoSelecionado) {
                    Swal.fire('Atenção!', 'Por favor, selecione um curso.', 'warning');
                    return;
                }
                passoAtual = 2;
            } else if (passoAtual === 2) {
                if (!$('#centro_id').val()) {
                    Swal.fire('Atenção!', 'Por favor, selecione um centro.', 'warning');
                    return;
                }
                passoAtual = 3;
                $('#selected_horario_id').val($('#horario_id').val());
            }
            
            atualizarPasso();
        }

        function passoAnterior() {
            if (passoAtual > 1) {
                passoAtual--;
                atualizarPasso();
            }
        }

        function atualizarPasso() {
            // Atualizar indicadores
            $('.step').removeClass('active completed');
            $('.step-line').removeClass('completed');
            
            for (let i = 1; i <= 3; i++) {
                if (i < passoAtual) {
                    $(`#step${i}`).addClass('completed');
                    if (i < 3) $(`#line${i}`).addClass('completed');
                } else if (i === passoAtual) {
                    $(`#step${i}`).addClass('active');
                }
            }

            // Mostrar/ocultar conteúdo
            $('.step-content').hide();
            $(`#passo${passoAtual}`).show();

            // Botões de navegação
            $('#btnAnterior').toggle(passoAtual > 1);
            $('#btnProximo').toggle(passoAtual < 3);
        }

        function mostrarInfoCentro() {
            if (centroSelecionado) {
                let contactosHtml = '';
                if (centroSelecionado.contactos) {
                    const contactos = JSON.parse(centroSelecionado.contactos);
                    contactosHtml = contactos.map(c => `${c.tipo}: ${c.valor}`).join('<br>');
                }

                const infoHtml = `
                    <h6><i class="fas fa-building me-2"></i>${centroSelecionado.nome}</h6>
                    <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>${centroSelecionado.localizacao}</p>
                    ${centroSelecionado.email ? `<p class="mb-2"><i class="fas fa-envelope me-2"></i>${centroSelecionado.email}</p>` : ''}
                    ${contactosHtml ? `<p class="mb-0"><i class="fas fa-phone me-2"></i>${contactosHtml}</p>` : ''}
                `;

                $('#centroInfo').html(infoHtml).show();
            }
        }

        function carregarHorarios() {
            const cursoId = $('#curso_id').val();
            const centroId = $('#centro_id').val();
            
            const horariosDisponiveis = horarios.filter(h => 
                h.curso_id == cursoId && h.centro_id == centroId
            );

            $('#horario_id').html('<option value="">Selecione um horário</option>');
            
            horariosDisponiveis.forEach(horario => {
                const horaTexto = horario.hora_inicio && horario.hora_fim 
                    ? ` (${horario.hora_inicio} - ${horario.hora_fim})`
                    : '';
                
                $('#horario_id').append(`
                    <option value="${horario.id}">
                        ${horario.dia_semana} - ${horario.periodo}${horaTexto}
                    </option>
                `);
            });
        }

        function adicionarContacto() {
            const novoContacto = `
                <div class="contacto-group">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <select class="form-select contacto-tipo">
                                <option value="telefone">Telefone</option>
                                <option value="telemovel">Telemóvel</option>
                                <option value="whatsapp">WhatsApp</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" class="form-control contacto-valor" placeholder="Número de contacto" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-contacto">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            $('#contactosContainer').append(novoContacto);
            atualizarBotoesRemover();
        }

        function atualizarBotoesRemover() {
            const totalContactos = $('.contacto-group').length;
            $('.remove-contacto').toggle(totalContactos > 1);
        }

        function submeterInscricao() {
            const contactos = [];
            $('.contacto-group').each(function() {
                const tipo = $(this).find('.contacto-tipo').val();
                const valor = $(this).find('.contacto-valor').val();
                if (valor.trim()) {
                    contactos.push({ tipo, valor });
                }
            });

            if (contactos.length === 0) {
                Swal.fire('Atenção!', 'Adicione pelo menos um contacto.', 'warning');
                return;
            }

            const dados = {
                curso_id: parseInt($('#curso_id').val()),
                centro_id: parseInt($('#selected_centro_id').val()),
                horario_id: $('#selected_horario_id').val() ? parseInt($('#selected_horario_id').val()) : null,
                nome_completo: $('#nome_completo').val(),
                email: $('#email').val() || null,
                contactos: JSON.stringify(contactos),
                observacoes: $('#observacoes').val() || null
            };

            $.ajax({
                url: '/api/pre-inscricoes',
                method: 'POST',
                data: JSON.stringify(dados),
                contentType: 'application/json',
                beforeSend: function() {
                    $('#inscricaoForm button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Enviando...');
                },
                success: function(response) {
                    $('#referenciaInscricao').text(`#${response.id}`);
                    $('#sucessoModal').modal('show');
                },
                error: function(xhr) {
                    let message = 'Ocorreu um erro ao submeter a pré-inscrição.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire('Erro!', message, 'error');
                },
                complete: function() {
                    $('#inscricaoForm button[type="submit"]').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Submeter Pré-Inscrição');
                }
            });
        }

        function novaInscricao() {
            location.reload();
        }

        // Inicialização
        $('#btnProximo').prop('disabled', true);
        atualizarBotoesRemover();
    </script>
</body>
</html>
