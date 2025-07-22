// Funções específicas para gestão de formadores

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
        
        // Limpar formulário se existir
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
            app.clearFormErrors(form);
        }
    }
}

// Gestão de contactos do formador
function adicionarContactoFormador() {
    const container = document.getElementById('contactos-container-formador');
    const div = document.createElement('div');
    div.className = 'contacto-input';
    div.innerHTML = `
        <input type="text" class="form-control contacto-field" placeholder="923456789" pattern="9[0-9]{8}" maxlength="9" required>
        <button type="button" class="btn btn-danger btn-sm btn-remover" onclick="removerContactoFormador(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
    atualizarBotoesRemoverFormador();
}

function removerContactoFormador(botao) {
    botao.parentElement.remove();
    atualizarBotoesRemoverFormador();
}

function atualizarBotoesRemoverFormador() {
    const inputs = document.querySelectorAll('#contactos-container-formador .contacto-input');
    inputs.forEach((input, index) => {
        const botao = input.querySelector('.btn-remover');
        botao.style.display = inputs.length > 1 ? 'inline-flex' : 'none';
    });
}

function coletarContactosFormador() {
    const inputs = document.querySelectorAll('#contactos-container-formador .contacto-field');
    const contactos = [];
    inputs.forEach(input => {
        if (input.value.trim()) {
            contactos.push(input.value.trim());
        }
    });
    return contactos;
}

function preencherContactosFormador(contactos) {
    const container = document.getElementById('contactos-container-formador');
    container.innerHTML = '';
    
    contactos.forEach((contacto, index) => {
        const div = document.createElement('div');
        div.className = 'contacto-input';
        div.innerHTML = `
            <input type="text" class="form-control contacto-field" value="${contacto}" placeholder="923456789" pattern="9[0-9]{8}" maxlength="9" required>
            <button type="button" class="btn btn-danger btn-sm btn-remover" onclick="removerContactoFormador(this)" ${index === 0 && contactos.length === 1 ? 'style="display:none;"' : ''}>
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
    });
    
    atualizarBotoesRemoverFormador();
}

// Preencher associações nos checkboxes
function preencherAssociacoes() {
    // Preencher centros
    const centrosContainer = document.getElementById('centros-checkbox-list');
    centrosContainer.innerHTML = centros.map(centro => `
        <div class="checkbox-item">
            <input type="checkbox" id="centro-${centro.id}" value="${centro.id}">
            <label for="centro-${centro.id}">${centro.nome}</label>
        </div>
    `).join('');
    
    // Preencher cursos
    const cursosContainer = document.getElementById('cursos-checkbox-list');
    cursosContainer.innerHTML = cursos.map(curso => `
        <div class="checkbox-item">
            <input type="checkbox" id="curso-${curso.id}" value="${curso.id}">
            <label for="curso-${curso.id}">${curso.nome}</label>
        </div>
    `).join('');
}

// Funções de modal
function abrirModalFormador(formador = null) {
    formadorEdicao = formador;
    const title = document.getElementById('modal-formador-title');
    const form = document.getElementById('form-formador');
    
    // Limpar formulário
    form.reset();
    app.clearFormErrors(form);
    
    if (formador) {
        title.innerHTML = '<i class="fas fa-edit"></i> Editar Formador';
        
        // Preencher campos
        document.getElementById('formador-nome').value = formador.nome || '';
        document.getElementById('formador-email').value = formador.email || '';
        document.getElementById('formador-especialidade').value = formador.especialidade || '';
        document.getElementById('formador-bio').value = formador.bio || '';
        document.getElementById('formador-foto-url').value = formador.foto_url || '';
        
        // Processar contactos
        let contactosArray = [];
        if (typeof formador.contactos === 'string') {
            contactosArray = formador.contactos.split(',').map(c => c.trim()).filter(c => c);
        } else if (Array.isArray(formador.contactos)) {
            contactosArray = formador.contactos;
        }
        preencherContactosFormador(contactosArray.length > 0 ? contactosArray : ['']);
        
        // Marcar centros associados
        const centrosAssociados = formador.centros || [];
        centrosAssociados.forEach(centro => {
            const checkbox = document.getElementById(`centro-${centro.id}`);
            if (checkbox) checkbox.checked = true;
        });
        
        // Marcar cursos associados
        const cursosAssociados = formador.cursos || [];
        cursosAssociados.forEach(curso => {
            const checkbox = document.getElementById(`curso-${curso.id}`);
            if (checkbox) checkbox.checked = true;
        });
        
    } else {
        title.innerHTML = '<i class="fas fa-plus"></i> Novo Formador';
        preencherContactosFormador(['']);
    }
    
    openModal('modal-formador');
}

function fecharModalFormador() {
    closeModal('modal-formador');
    formadorEdicao = null;
}

// Funções CRUD
function editarFormador(id) {
    const formador = formadores.find(f => f.id == id);
    if (formador) {
        abrirModalFormador(formador);
    }
}

function confirmarDeleteFormador(id) {
    const formador = formadores.find(f => f.id == id);
    
    if (formador) {
        formadorDelete = formador;
        
        document.getElementById('delete-formador-info').innerHTML = `
            <div><strong>Nome:</strong> ${formador.nome}</div>
            <div><strong>Email:</strong> ${formador.email || 'Não informado'}</div>
            <div><strong>Especialidade:</strong> ${formador.especialidade || 'Não informado'}</div>
            <div><strong>Centros associados:</strong> ${formador.centros ? formador.centros.length : 0}</div>
            <div><strong>Cursos associados:</strong> ${formador.cursos ? formador.cursos.length : 0}</div>
        `;
        
        openModal('modal-delete-formador');
    }
}

function fecharModalDeleteFormador() {
    closeModal('modal-delete-formador');
    formadorDelete = null;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Formulário de formador
    document.getElementById('form-formador').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            nome: document.getElementById('formador-nome').value,
            email: document.getElementById('formador-email').value || null,
            contactos: coletarContactosFormador(),
            especialidade: document.getElementById('formador-especialidade').value || null,
            bio: document.getElementById('formador-bio').value || null,
            foto_url: document.getElementById('formador-foto-url').value || null
        };
        
        // Coletar centros selecionados
        const centrosSelecionados = [];
        document.querySelectorAll('#centros-checkbox-list input[type="checkbox"]:checked').forEach(checkbox => {
            centrosSelecionados.push(parseInt(checkbox.value));
        });
        if (centrosSelecionados.length > 0) {
            formData.centros = centrosSelecionados;
        }
        
        // Coletar cursos selecionados
        const cursosSelecionados = [];
        document.querySelectorAll('#cursos-checkbox-list input[type="checkbox"]:checked').forEach(checkbox => {
            cursosSelecionados.push(parseInt(checkbox.value));
        });
        if (cursosSelecionados.length > 0) {
            formData.cursos = cursosSelecionados;
        }
        
        const btnSubmit = document.getElementById('btn-salvar-formador');
        const originalText = btnSubmit.innerHTML;
        
        try {
            app.setButtonLoading(btnSubmit, true);
            
            const url = formadorEdicao 
                ? `${APP_CONFIG.API_BASE}/formadores/${formadorEdicao.id}` 
                : `${APP_CONFIG.API_BASE}/formadores`;
            const method = formadorEdicao ? 'PUT' : 'POST';
            
            await app.makeRequest(url, method, formData);
            
            app.showAlert('success', `Formador ${formadorEdicao ? 'atualizado' : 'cadastrado'} com sucesso!`);
            fecharModalFormador();
            carregarTodosOsDados();
            
        } catch (error) {
            if (error.errors) {
                app.showFormErrors(this, error.errors);
            } else {
                app.showAlert('danger', error.message || 'Erro ao salvar formador');
            }
        } finally {
            app.setButtonLoading(btnSubmit, false, originalText);
        }
    });
    
    // Botão de confirmar delete
    document.getElementById('btn-confirmar-delete-formador').addEventListener('click', async function() {
        if (!formadorDelete) return;
        
        const btn = this;
        const originalText = btn.innerHTML;
        
        try {
            app.setButtonLoading(btn, true);
            
            await app.makeRequest(`${APP_CONFIG.API_BASE}/formadores/${formadorDelete.id}`, 'DELETE');
            
            app.showAlert('success', 'Formador excluído com sucesso!');
            fecharModalDeleteFormador();
            carregarTodosOsDados();
            
        } catch (error) {
            app.showAlert('danger', error.message || 'Erro ao excluir formador');
        } finally {
            app.setButtonLoading(btn, false, originalText);
        }
    });
});

// Funções auxiliares
function limparFiltros() {
    document.getElementById('busca-formador').value = '';
    document.getElementById('filtro-especialidade').value = '';
    aplicarFiltros();
}

function exportarFormadores() {
    if (formadores.length === 0) {
        app.showAlert('warning', 'Não há formadores para exportar');
        return;
    }
    
    const dadosExport = formadores.map(formador => {
        return {
            id: formador.id,
            nome: formador.nome,
            email: formador.email || 'Não informado',
            especialidade: formador.especialidade || 'Não informado',
            contactos: Array.isArray(formador.contactos) ? formador.contactos.join(', ') : formador.contactos,
            centros_associados: formador.centros ? formador.centros.length : 0,
            cursos_associados: formador.cursos ? formador.cursos.length : 0,
            bio: formador.bio || 'Não informado'
        };
    });
    
    app.exportData(dadosExport, 'formadores_' + new Date().toISOString().split('T')[0], 'csv');
}
