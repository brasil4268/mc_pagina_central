// Funções específicas para gestão de horários

// Funções auxiliares para modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
            app.clearFormErrors(form);
        }
    }
}

// Atualizar estatísticas
function atualizarEstatisticas() {
    const totalHorarios = horarios.length;
    const horariosManha = horarios.filter(h => h.periodo === 'manha').length;
    const horariosTarde = horarios.filter(h => h.periodo === 'tarde').length;
    const horariosNoite = horarios.filter(h => h.periodo === 'noite').length;

    document.getElementById('total-horarios').textContent = totalHorarios;
    document.getElementById('horarios-manha').textContent = horariosManha;
    document.getElementById('horarios-tarde').textContent = horariosTarde;
    document.getElementById('horarios-noite').textContent = horariosNoite;
}

// Preencher select de cursos
function preencherSelectCursos() {
    const select = document.getElementById('horario-curso-id');
    select.innerHTML = '<option value="">Selecione um curso</option>';
    
    cursos.forEach(curso => {
        select.innerHTML += `<option value="${curso.id}">${curso.nome}</option>`;
    });
}

// Configurar filtros
function configurarFiltros() {
    const filtros = ['busca-horario', 'filtro-dia', 'filtro-periodo'];
    
    filtros.forEach(filtroId => {
        const elemento = document.getElementById(filtroId);
        if (elemento) {
            elemento.addEventListener('input', aplicarFiltros);
            elemento.addEventListener('change', aplicarFiltros);
        }
    });
}

// Aplicar filtros
function aplicarFiltros() {
    const busca = document.getElementById('busca-horario').value.toLowerCase();
    const dia = document.getElementById('filtro-dia').value;
    const periodo = document.getElementById('filtro-periodo').value;

    let horariosFiltrados = horarios.filter(horario => {
        const curso = cursos.find(c => c.id == horario.curso_id);
        const nomeCurso = curso ? curso.nome.toLowerCase() : '';
        
        const matchBusca = !busca || nomeCurso.includes(busca);
        const matchDia = !dia || horario.dia_semana === dia;
        const matchPeriodo = !periodo || horario.periodo === periodo;

        return matchBusca && matchDia && matchPeriodo;
    });

    if (visualizacaoAtual === 'calendario') {
        renderizarCalendario(horariosFiltrados);
    } else {
        renderizarLista(horariosFiltrados);
    }
}

// Renderizar calendário
function renderizarCalendario(horariosParaRender) {
    // Limpar todas as colunas
    Object.keys(diasSemana).forEach(dia => {
        const coluna = document.getElementById(`dia-${dia}`);
        if (coluna) coluna.innerHTML = '';
    });

    if (horariosParaRender.length === 0) {
        document.getElementById('empty-state').style.display = 'block';
        return;
    }

    document.getElementById('empty-state').style.display = 'none';

    // Agrupar por dia da semana
    const horariosPorDia = {};
    horariosParaRender.forEach(horario => {
        if (!horariosPorDia[horario.dia_semana]) {
            horariosPorDia[horario.dia_semana] = [];
        }
        horariosPorDia[horario.dia_semana].push(horario);
    });

    // Renderizar cada dia
    Object.keys(horariosPorDia).forEach(dia => {
        const coluna = document.getElementById(`dia-${dia}`);
        if (!coluna) return;

        // Ordenar por período
        const ordemPeriodo = { 'manha': 1, 'tarde': 2, 'noite': 3 };
        horariosPorDia[dia].sort((a, b) => ordemPeriodo[a.periodo] - ordemPeriodo[b.periodo]);

        coluna.innerHTML = horariosPorDia[dia].map(horario => {
            const curso = cursos.find(c => c.id == horario.curso_id);
            const nomeCurso = curso ? curso.nome : 'Curso não encontrado';
            
            return `
                <div class="horario-item" onclick="editarHorario(${horario.id})">
                    <div class="horario-curso">${nomeCurso}</div>
                    <div class="horario-periodo">
                        <span class="periodo-tag periodo-${horario.periodo}">
                            ${horario.periodo}
                        </span>
                        <button class="btn btn-danger btn-sm" style="margin-left: auto; padding: 2px 6px;" 
                                onclick="event.stopPropagation(); confirmarDeleteHorario(${horario.id})">
                            <i class="fas fa-trash" style="font-size: 0.7rem;"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    });
}

// Renderizar lista
function renderizarLista(horariosParaRender) {
    const container = document.getElementById('horarios-lista-container');
    
    if (horariosParaRender.length === 0) {
        container.innerHTML = '';
        document.getElementById('empty-state').style.display = 'block';
        return;
    }

    document.getElementById('empty-state').style.display = 'none';

    // Agrupar por dia da semana
    const horariosPorDia = {};
    horariosParaRender.forEach(horario => {
        if (!horariosPorDia[horario.dia_semana]) {
            horariosPorDia[horario.dia_semana] = [];
        }
        horariosPorDia[horario.dia_semana].push(horario);
    });

    container.innerHTML = Object.keys(horariosPorDia).map(dia => {
        const ordemPeriodo = { 'manha': 1, 'tarde': 2, 'noite': 3 };
        horariosPorDia[dia].sort((a, b) => ordemPeriodo[a.periodo] - ordemPeriodo[b.periodo]);

        return `
            <div class="horario-card">
                <div class="horario-header">
                    <div class="dia-badge">${diasSemana[dia]}</div>
                    <div style="color: #666;">${horariosPorDia[dia].length} horário(s)</div>
                </div>
                
                <div style="display: grid; gap: 10px;">
                    ${horariosPorDia[dia].map(horario => {
                        const curso = cursos.find(c => c.id == horario.curso_id);
                        const nomeCurso = curso ? curso.nome : 'Curso não encontrado';
                        
                        return `
                            <div style="display: flex; justify-content: space-between; align-items: center; 
                                        padding: 12px; background: #f8fafc; border-radius: 8px; 
                                        border-left: 4px solid var(--secondary-color);">
                                <div>
                                    <div style="font-weight: 600; color: var(--primary-color);">${nomeCurso}</div>
                                    <span class="periodo-tag periodo-${horario.periodo}">${horario.periodo}</span>
                                </div>
                                <div style="display: flex; gap: 5px;">
                                    <button class="btn btn-warning btn-sm" onclick="editarHorario(${horario.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmarDeleteHorario(${horario.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `;
    }).join('');
}

// Alterar visualização
function alterarVisualizacao(tipo) {
    visualizacaoAtual = tipo;
    
    // Atualizar botões
    document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(`btn-${tipo}`).classList.add('active');
    
    // Mostrar/ocultar visualizações
    document.getElementById('view-calendario').style.display = tipo === 'calendario' ? 'block' : 'none';
    document.getElementById('view-lista').style.display = tipo === 'lista' ? 'block' : 'none';
    
    // Re-renderizar
    aplicarFiltros();
}

// Funções de modal
function abrirModalHorario(horario = null) {
    horarioEdicao = horario;
    const title = document.getElementById('modal-horario-title');
    const form = document.getElementById('form-horario');
    
    form.reset();
    app.clearFormErrors(form);
    
    if (horario) {
        title.innerHTML = '<i class="fas fa-edit"></i> Editar Horário';
        
        document.getElementById('horario-curso-id').value = horario.curso_id || '';
        document.getElementById('horario-dia-semana').value = horario.dia_semana || '';
        document.getElementById('horario-periodo').value = horario.periodo || '';
    } else {
        title.innerHTML = '<i class="fas fa-plus"></i> Novo Horário';
    }
    
    openModal('modal-horario');
}

function fecharModalHorario() {
    closeModal('modal-horario');
    horarioEdicao = null;
}

// Funções CRUD
function editarHorario(id) {
    const horario = horarios.find(h => h.id == id);
    if (horario) {
        abrirModalHorario(horario);
    }
}

function confirmarDeleteHorario(id) {
    const horario = horarios.find(h => h.id == id);
    
    if (horario) {
        horarioDelete = horario;
        const curso = cursos.find(c => c.id == horario.curso_id);
        
        document.getElementById('delete-horario-info').innerHTML = `
            <div><strong>Curso:</strong> ${curso ? curso.nome : 'Não encontrado'}</div>
            <div><strong>Dia:</strong> ${diasSemana[horario.dia_semana]}</div>
            <div><strong>Período:</strong> ${horario.periodo}</div>
        `;
        
        openModal('modal-delete-horario');
    }
}

function fecharModalDeleteHorario() {
    closeModal('modal-delete-horario');
    horarioDelete = null;
}

function limparFiltros() {
    document.getElementById('busca-horario').value = '';
    document.getElementById('filtro-dia').value = '';
    document.getElementById('filtro-periodo').value = '';
    aplicarFiltros();
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Formulário de horário
    document.getElementById('form-horario').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            curso_id: document.getElementById('horario-curso-id').value,
            dia_semana: document.getElementById('horario-dia-semana').value,
            periodo: document.getElementById('horario-periodo').value
        };
        
        const btnSubmit = document.getElementById('btn-salvar-horario');
        const originalText = btnSubmit.innerHTML;
        
        try {
            app.setButtonLoading(btnSubmit, true);
            
            const url = horarioEdicao 
                ? `${APP_CONFIG.API_BASE}/horarios/${horarioEdicao.id}` 
                : `${APP_CONFIG.API_BASE}/horarios`;
            const method = horarioEdicao ? 'PUT' : 'POST';
            
            await app.makeRequest(url, method, formData);
            
            app.showAlert('success', `Horário ${horarioEdicao ? 'atualizado' : 'cadastrado'} com sucesso!`);
            fecharModalHorario();
            carregarDados();
            
        } catch (error) {
            if (error.errors) {
                app.showFormErrors(this, error.errors);
            } else {
                app.showAlert('danger', error.message || 'Erro ao salvar horário');
            }
        } finally {
            app.setButtonLoading(btnSubmit, false, originalText);
        }
    });
    
    // Botão de confirmar delete
    document.getElementById('btn-confirmar-delete-horario').addEventListener('click', async function() {
        if (!horarioDelete) return;
        
        const btn = this;
        const originalText = btn.innerHTML;
        
        try {
            app.setButtonLoading(btn, true);
            
            await app.makeRequest(`${APP_CONFIG.API_BASE}/horarios/${horarioDelete.id}`, 'DELETE');
            
            app.showAlert('success', 'Horário excluído com sucesso!');
            fecharModalDeleteHorario();
            carregarDados();
            
        } catch (error) {
            app.showAlert('danger', error.message || 'Erro ao excluir horário');
        } finally {
            app.setButtonLoading(btn, false, originalText);
        }
    });
});
