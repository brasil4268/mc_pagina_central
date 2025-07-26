// FormAcademy Admin - Versão Melhorada
// =====================================

// Configuration
const ADMIN_CONFIG = {
    API_BASE_URL: '/api',
    ITEMS_PER_PAGE: 10,
    DEBOUNCE_DELAY: 300,
    AUTO_REFRESH_INTERVAL: 30000, // 30 segundos
    MAX_RETRIES: 3,
    RETRY_DELAY: 1000
};

// Estado global melhorado
class AdminState {
    constructor() {
        this.data = {
            centros: [],
            cursos: [],
            formadores: [],
            horarios: [],
            preInscricoes: []
        };
        this.ui = {
            currentSection: 'dashboard',
            sidebarCollapsed: false,
            loading: false,
            currentModal: null,
            currentEditId: null,
            currentEditType: null,
            pagination: {},
            filters: {},
            searchTerms: {}
        };
        this.listeners = new Map();
        this.autoRefreshInterval = null;
        this.notifications = [];
        this.lastDataCheck = Date.now();
    }

    setState(key, value) {
        this.data[key] = value;
        this.notify(key, value);
    }

    getState(key) {
        return this.data[key];
    }

    subscribe(event, callback) {
        if (!this.listeners.has(event)) {
            this.listeners.set(event, []);
        }
        this.listeners.get(event).push(callback);
    }

    notify(event, data) {
        if (this.listeners.has(event)) {
            this.listeners.get(event).forEach(callback => callback(data));
        }
    }
}

const adminState = new AdminState();

// Utilitários melhorados
// Validações melhoradas
const AdminValidation = {
    // Validação de email
    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },

    // Validação de telefone angolano (9XX XXX XXX)
    validatePhone(phone) {
        const phoneRegex = /^9\d{8}$/;
        const cleanPhone = phone.replace(/\D/g, '');
        return phoneRegex.test(cleanPhone);
    },

    // Validação de nome (mínimo 2 caracteres, apenas letras e espaços)
    validateName(name) {
        const nameRegex = /^[a-zA-ZÀ-ÿ\s]{2,}$/;
        return nameRegex.test(name.trim());
    },

    // Validação de preço (apenas números positivos)
    validatePrice(price) {
        const priceNum = parseFloat(price);
        return !isNaN(priceNum) && priceNum > 0;
    },

    // Validação de duração (apenas números positivos)
    validateDuration(duration) {
        const durationNum = parseInt(duration);
        return !isNaN(durationNum) && durationNum > 0;
    },

    // Validação de array de contactos
    validateContacts(contacts) {
        if (!Array.isArray(contacts)) return false;
        if (contacts.length === 0) return false;
        return contacts.every(contact => this.validatePhone(contact));
    },

    // Mensagens de erro
    getErrorMessage(field, value) {
        switch (field) {
            case 'email':
                return 'Por favor, insira um email válido';
            case 'phone':
                return 'Telefone deve ter formato 9XX XXX XXX';
            case 'name':
                return 'Nome deve ter pelo menos 2 caracteres';
            case 'price':
                return 'Preço deve ser um número positivo';
            case 'duration':
                return 'Duração deve ser um número positivo';
            case 'contacts':
                return 'Pelo menos um contacto válido é obrigatório';
            default:
                return 'Campo inválido';
        }
    },

    // Validação de formulário completo
    validateForm(formData, requiredFields) {
        const errors = {};
        
        requiredFields.forEach(field => {
            const value = formData[field];
            
            if (!value || (typeof value === 'string' && !value.trim())) {
                errors[field] = 'Este campo é obrigatório';
                return;
            }

            // Validações específicas por tipo de campo
            switch (field) {
                case 'email':
                    if (!this.validateEmail(value)) {
                        errors[field] = this.getErrorMessage('email');
                    }
                    break;
                case 'nome':
                    if (!this.validateName(value)) {
                        errors[field] = this.getErrorMessage('name');
                    }
                    break;
                case 'preco':
                    if (!this.validatePrice(value)) {
                        errors[field] = this.getErrorMessage('price');
                    }
                    break;
                case 'duracao':
                    if (!this.validateDuration(value)) {
                        errors[field] = this.getErrorMessage('duration');
                    }
                    break;
                case 'contactos':
                    if (Array.isArray(value) && value.length > 0) {
                        const invalidContacts = value.filter(contact => !this.validatePhone(contact));
                        if (invalidContacts.length > 0) {
                            errors[field] = 'Alguns contactos têm formato inválido';
                        }
                    } else {
                        errors[field] = this.getErrorMessage('contacts');
                    }
                    break;
            }
        });

        return {
            isValid: Object.keys(errors).length === 0,
            errors
        };
    },

    // Formatação de valores
    formatInput(field, value) {
        switch (field) {
            case 'phone':
                return this.formatPhoneInput(value);
            case 'email':
                return value.toLowerCase().trim();
            case 'name':
                return value.trim().replace(/\s+/g, ' ');
            case 'price':
                return parseFloat(value) || 0;
            case 'duration':
                return parseInt(value) || 0;
            default:
                return value;
        }
    },

    formatPhoneInput(phone) {
        const cleaned = phone.replace(/\D/g, '');
        if (cleaned.length <= 9) {
            return cleaned;
        }
        return cleaned.substring(0, 9);
    }
};

const AdminUtils = {
    formatCurrency(amount) {
        return new Intl.NumberFormat('pt-AO', {
            style: 'currency',
            currency: 'AOA',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    },

    formatDate(dateString) {
        return new Intl.DateTimeFormat('pt-BR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(dateString));
    },

    parseContacts(contacts) {
        if (Array.isArray(contacts)) {
            return contacts.filter(contact => contact && contact.trim());
        } else if (typeof contacts === 'string') {
            try {
                const parsed = JSON.parse(contacts);
                return Array.isArray(parsed) ? parsed.filter(contact => contact && contact.trim()) : [contacts];
            } catch {
                return contacts.trim() ? [contacts.trim()] : [];
            }
        }
        return [];
    },

    formatContactsDisplay(contacts) {
        const contactsArray = this.parseContacts(contacts);
        if (contactsArray.length === 0) return 'Sem contacto';
        
        // Exibe todos os contactos com flex wrap
        const contactsHtml = contactsArray.map(contacto => 
            `<span class="contacto-tag"><i class="fas fa-phone"></i> ${this.formatPhoneNumber(contacto)}</span>`
        ).join('');
        
        return `<div class="contactos-list">${contactsHtml}</div>`;
    },

    formatPhoneNumber(phone) {
        if (!phone) return '';
        const cleanPhone = phone.replace(/\D/g, '');
        if (cleanPhone.length === 9 && cleanPhone.startsWith('9')) {
            return `${cleanPhone.slice(0, 3)} ${cleanPhone.slice(3, 6)} ${cleanPhone.slice(6)}`;
        }
        return phone;
    },

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    animateCounter(element, start, end, duration = 1500) {
        const startTime = performance.now();
        
        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const currentValue = Math.floor(start + (end - start) * easeOut);
            
            element.textContent = currentValue;
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            }
        }
        
        requestAnimationFrame(updateCounter);
    }
};

// Serviço de API melhorado
class AdminApiService {
    constructor(baseURL) {
        this.baseURL = baseURL;
        this.retryCount = 0;
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...options.headers
            },
            ...options
        };

        try {
            AdminUI.showLoading();
            const response = await fetch(url, config);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            this.retryCount = 0;
            AdminUI.hideLoading();
            
            return data.dados || data;
        } catch (error) {
            console.error(`Admin API Error (${endpoint}):`, error);
            AdminUI.hideLoading();
            
            if (this.retryCount < ADMIN_CONFIG.MAX_RETRIES && this.isRetryableError(error)) {
                this.retryCount++;
                await this.delay(ADMIN_CONFIG.RETRY_DELAY * this.retryCount);
                return this.request(endpoint, options);
            }
            
            throw error;
        }
    }

    isRetryableError(error) {
        return error.message.includes('Failed to fetch') || 
               error.message.includes('NetworkError') ||
               error.message.includes('500') ||
               error.message.includes('502') ||
               error.message.includes('503');
    }

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    async getAll(resource) {
        return this.request(`/${resource}`);
    }

    async getById(resource, id) {
        return this.request(`/${resource}/${id}`);
    }

    async create(resource, data) {
        return this.request(`/${resource}`, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async update(resource, id, data) {
        return this.request(`/${resource}/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async delete(resource, id) {
        return this.request(`/${resource}/${id}`, {
            method: 'DELETE'
        });
    }
}

const adminApi = new AdminApiService(ADMIN_CONFIG.API_BASE_URL);

// Interface do usuário melhorada
class AdminUI {
    static showLoading(show = true) {
        const loading = document.getElementById('loading');
        if (loading) {
            if (show) {
                loading.classList.add('show');
            } else {
                loading.classList.remove('show');
            }
        }
    }

    static hideLoading() {
        this.showLoading(false);
    }

    static showNotification(message, type = 'success', duration = 5000) {
        const notification = document.getElementById('notification');
        if (!notification) return;

        const icon = notification.querySelector('.notification-icon');
        const text = notification.querySelector('.notification-text');

        // Set icon based on type
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };

        icon.className = `notification-icon ${icons[type] || icons.info}`;
        text.textContent = message;
        
        notification.className = `notification show ${type}`;

        if (duration > 0) {
            setTimeout(() => this.hideNotification(), duration);
        }
    }

    static hideNotification() {
        const notification = document.getElementById('notification');
        if (notification) {
            notification.classList.remove('show');
        }
    }

    static showConfirmModal(title, message, onConfirm, onCancel = null) {
        const modal = document.createElement('div');
        modal.className = 'confirm-modal';
        modal.innerHTML = `
            <div class="modal-overlay"></div>
            <div class="modal-container">
                <div class="modal-header">
                    <h3>${title}</h3>
                    <button class="modal-close" onclick="this.closest('.confirm-modal').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-content">
                    <p>${message}</p>
                </div>
                <div class="modal-actions">
                    <button class="btn btn-secondary cancel-btn">Cancelar</button>
                    <button class="btn btn-danger confirm-btn">Confirmar</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        modal.querySelector('.confirm-btn').onclick = () => {
            modal.remove();
            if (onConfirm) onConfirm();
        };

        modal.querySelector('.cancel-btn').onclick = () => {
            modal.remove();
            if (onCancel) onCancel();
        };

        modal.querySelector('.modal-overlay').onclick = () => {
            modal.remove();
            if (onCancel) onCancel();
        };
    }
}

// Controlador de dados melhorado
class AdminDataController {
    constructor() {
        this.data = {};
    }

    async loadAllData() {
        try {
            AdminUI.showLoading();
            
            // Carrega dados em paralelo para melhor performance
            const [centros, cursos, formadores, horarios, preInscricoes] = await Promise.all([
                adminApi.getAll('centros').catch(() => []),
                adminApi.getAll('cursos').catch(() => []),
                adminApi.getAll('formadores').catch(() => []),
                adminApi.getAll('horarios').catch(() => []),
                adminApi.getAll('pre-inscricoes').catch(() => [])
            ]);

            this.data = { centros, cursos, formadores, horarios, preInscricoes };
            
            // Atualiza estado
            adminState.setState('centros', centros);
            adminState.setState('cursos', cursos);
            adminState.setState('formadores', formadores);
            adminState.setState('horarios', horarios);
            adminState.setState('preInscricoes', preInscricoes);

            // Atualiza dashboard
            this.updateDashboard();
            
            // Atualiza tabelas se estiver na seção correspondente
            this.updateCurrentSection();

            AdminUI.hideLoading();
            console.log('✅ Dados carregados com sucesso');
            
        } catch (error) {
            console.error('Erro ao carregar dados:', error);
            AdminUI.hideLoading();
            AdminUI.showNotification('Erro ao carregar dados do sistema', 'error');
        }
    }

    updateDashboard() {
        // Atualiza contadores com animação
        const elements = {
            'total-centros': this.data.centros?.length || 0,
            'total-cursos': this.data.cursos?.length || 0,
            'total-formadores': this.data.formadores?.length || 0,
            'total-pre-inscricoes': this.data.preInscricoes?.length || 0
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                const currentValue = parseInt(element.textContent) || 0;
                AdminUtils.animateCounter(element, currentValue, value);
            }
        });

        // Atualiza estatísticas do header
        const headerCourses = document.getElementById('header-total-courses');
        const headerRegistrations = document.getElementById('header-total-registrations');
        
        if (headerCourses) headerCourses.textContent = this.data.cursos?.length || 0;
        if (headerRegistrations) headerRegistrations.textContent = this.data.preInscricoes?.length || 0;

        // Renderiza gráficos
        this.renderCharts();
    }

    renderCharts() {
        this.renderCoursesByCenter();
        this.renderRecentRegistrations();
    }

    renderCoursesByCenter() {
        const container = document.getElementById('courses-by-center-chart');
        if (!container || !this.data.centros || !this.data.cursos) return;

        // Agrupa cursos por centro
        const coursesByCenter = {};
        this.data.centros.forEach(centro => {
            coursesByCenter[centro.nome] = 0;
        });

        this.data.cursos.forEach(curso => {
            const centro = this.data.centros.find(c => c.id === curso.centro_id);
            if (centro) {
                coursesByCenter[centro.nome]++;
            }
        });

        // Renderiza gráfico de barras simples
        const chartHTML = Object.entries(coursesByCenter)
            .map(([centro, count]) => {
                const percentage = this.data.cursos.length > 0 ? (count / this.data.cursos.length) * 100 : 0;
                return `
                    <div class="chart-bar-item">
                        <div class="bar-label">${centro}</div>
                        <div class="bar-container">
                            <div class="bar-fill" style="width: ${percentage}%"></div>
                            <span class="bar-value">${count}</span>
                        </div>
                    </div>
                `;
            })
            .join('');

        container.innerHTML = `<div class="simple-bar-chart">${chartHTML}</div>`;
    }

    renderRecentRegistrations() {
        const container = document.getElementById('recent-registrations');
        if (!container || !this.data.preInscricoes) return;

        // Pega as 5 inscrições mais recentes
        const recent = this.data.preInscricoes
            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
            .slice(0, 5);

        const listHTML = recent.map(inscricao => {
            const curso = this.data.cursos?.find(c => c.id === inscricao.curso_id);
            return `
                <div class="recent-item">
                    <div class="recent-info">
                        <div class="recent-name">${inscricao.nome_completo || inscricao.nome || 'Nome não disponível'}</div>
                        <div class="recent-course">${curso?.nome || 'Curso não encontrado'}</div>
                    </div>
                    <div class="recent-meta">
                        <span class="recent-status status-${inscricao.status}">${inscricao.status}</span>
                        <span class="recent-date">${AdminUtils.formatDate(inscricao.created_at)}</span>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = recent.length > 0 
            ? `<div class="recent-list">${listHTML}</div>`
            : '<div class="no-data">Nenhuma pré-inscrição encontrada</div>';
    }

    updateCurrentSection() {
        const currentSection = adminState.ui.currentSection;
        
        switch (currentSection) {
            case 'centros':
                this.renderCentrosTable();
                break;
            case 'cursos':
                this.renderCursosTable();
                break;
            case 'formadores':
                this.renderFormadoresTable();
                break;
            case 'horarios':
                this.renderHorariosTable();
                break;
            case 'pre-inscricoes':
                this.renderPreInscricoesTable();
                break;
            case 'calendario':
                this.renderCalendar();
                break;
        }
        
        // Populate filter options when switching sections
        if (navigationController) {
            navigationController.populateFilterOptions();
        }
    }

    renderCentrosTable() {
        const tbody = document.querySelector('#centros-table tbody');
        if (!tbody || !this.data.centros) return;

        const html = this.data.centros.map(centro => `
            <tr>
                <td>
                    <div class="table-name">
                        <i class="fas fa-building text-primary"></i>
                        <span>${centro.nome}</span>
                    </div>
                </td>
                <td>
                    <div class="location-info">
                        <i class="fas fa-map-marker-alt text-muted"></i>
                        <span>${centro.localizacao}</span>
                    </div>
                </td>
                <td>
                    <a href="mailto:${centro.email}" class="email-link">
                        <i class="fas fa-envelope"></i>
                        ${centro.email}
                    </a>
                </td>
                <td>
                    <div class="contacts-cell">
                        ${AdminUtils.formatContactsDisplay(centro.contactos)}
                    </div>
                </td>
                <td>
                    <span class="badge badge-info">
                        ${this.data.cursos?.filter(c => c.centro_id === centro.id).length || 0} cursos
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <button class="btn-action btn-view" onclick="viewItem('centro', ${centro.id})" title="Ver detalhes">
                            <i class="fas fa-eye"></i>
                            <span>Detalhes</span>
                        </button>
                        <button class="btn-action btn-edit" onclick="editItem('centro', ${centro.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                            <span>Editar</span>
                        </button>
                        <button class="btn-action btn-delete" onclick="deleteItem('centro', ${centro.id})" title="Remover">
                            <i class="fas fa-trash"></i>
                            <span>Remover</span>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        tbody.innerHTML = html;
        this.updateTableInfo('centros', this.data.centros.length);
    }

    renderCursosTable() {
        const tbody = document.querySelector('#cursos-table tbody');
        if (!tbody || !this.data.cursos) return;

        const html = this.data.cursos.map(curso => {
            const centro = this.data.centros?.find(c => c.id === curso.centro_id);
            return `
                <tr>
                    <td>
                        <div class="table-name">
                            <i class="fas fa-graduation-cap text-primary"></i>
                            <span>${curso.nome}</span>
                        </div>
                    </td>
                    <td>
                        <span class="center-name">${centro?.nome || 'Centro não encontrado'}</span>
                    </td>
                    <td>
                        <span class="course-area">${curso.area}</span>
                    </td>
                    <td>
                        <span class="course-duration">${curso.duracao} horas</span>
                    </td>
                    <td>
                        <span class="course-price">${AdminUtils.formatCurrency(curso.preco)}</span>
                    </td>
                    <td>
                        <span class="course-modality">${curso.modalidade}</span>
                    </td>
                    <td>
                        <span class="badge ${curso.ativo ? 'badge-success' : 'badge-danger'}">
                            ${curso.ativo ? 'Ativo' : 'Inativo'}
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action btn-view" onclick="viewItem('curso', ${curso.id})" title="Ver detalhes">
                                <i class="fas fa-eye"></i>
                                <span>Detalhes</span>
                            </button>
                            <button class="btn-action btn-edit" onclick="editItem('curso', ${curso.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                                <span>Editar</span>
                            </button>
                            <button class="btn-action btn-delete" onclick="deleteItem('curso', ${curso.id})" title="Remover">
                                <i class="fas fa-trash"></i>
                                <span>Remover</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        tbody.innerHTML = html;
        this.updateTableInfo('cursos', this.data.cursos.length);
    }

    renderFormadoresTable() {
        const tbody = document.querySelector('#formadores-table tbody');
        if (!tbody || !this.data.formadores) return;

        const html = this.data.formadores.map(formador => `
            <tr>
                <td>
                    <div class="table-name">
                        <i class="fas fa-chalkboard-teacher text-primary"></i>
                        <span>${formador.nome}</span>
                    </div>
                </td>
                <td>
                    <a href="mailto:${formador.email}" class="email-link">
                        <i class="fas fa-envelope"></i>
                        ${formador.email}
                    </a>
                </td>
                <td>
                    <span class="specialty">${formador.especialidade}</span>
                </td>
                <td>
                    <div class="contacts-cell">
                        ${AdminUtils.formatContactsDisplay(formador.contactos)}
                    </div>
                </td>
                <td>
                    <span class="badge badge-info">
                        ${this.data.cursos?.filter(c => c.formador_id === formador.id).length || 0} cursos
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <button class="btn-action btn-view" onclick="viewItem('formador', ${formador.id})" title="Ver detalhes">
                            <i class="fas fa-eye"></i>
                            <span>Detalhes</span>
                        </button>
                        <button class="btn-action btn-edit" onclick="editItem('formador', ${formador.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                            <span>Editar</span>
                        </button>
                        <button class="btn-action btn-delete" onclick="deleteItem('formador', ${formador.id})" title="Remover">
                            <i class="fas fa-trash"></i>
                            <span>Remover</span>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        tbody.innerHTML = html;
        this.updateTableInfo('formadores', this.data.formadores.length);
    }

    renderHorariosTable() {
        const tbody = document.querySelector('#horarios-table tbody');
        if (!tbody || !this.data.horarios) return;

        const html = this.data.horarios.map(horario => {
            const curso = this.data.cursos?.find(c => c.id === horario.curso_id);
            return `
                <tr>
                    <td>
                        <span class="course-name">${curso?.nome || 'Curso não encontrado'}</span>
                    </td>
                    <td>
                        <span class="day-week">${horario.dia_semana}</span>
                    </td>
                    <td>
                        <span class="period">${horario.periodo}</span>
                    </td>
                    <td>
                        <span class="time">${horario.hora_inicio}</span>
                    </td>
                    <td>
                        <span class="time">${horario.hora_fim}</span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action btn-edit" onclick="editItem('horario', ${horario.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                                <span>Editar</span>
                            </button>
                            <button class="btn-action btn-delete" onclick="deleteItem('horario', ${horario.id})" title="Remover">
                                <i class="fas fa-trash"></i>
                                <span>Remover</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        tbody.innerHTML = html;
        this.updateTableInfo('horarios', this.data.horarios.length);
    }

    renderPreInscricoesTable() {
        const tbody = document.querySelector('#pre-inscricoes-table tbody');
        if (!tbody || !this.data.preInscricoes) return;

        const html = this.data.preInscricoes.map(inscricao => {
            const curso = this.data.cursos?.find(c => c.id === inscricao.curso_id);
            const centro = this.data.centros?.find(c => c.id === curso?.centro_id);
            return `
                <tr>
                    <td>
                        <div class="table-name">
                            <i class="fas fa-user text-primary"></i>
                            <span>${inscricao.nome_completo || inscricao.nome || 'Nome não disponível'}</span>
                        </div>
                    </td>
                    <td>
                        <a href="mailto:${inscricao.email}" class="email-link">
                            <i class="fas fa-envelope"></i>
                            ${inscricao.email}
                        </a>
                    </td>
                    <td>
                        <span class="course-name">${curso?.nome || 'Curso não encontrado'}</span>
                    </td>
                    <td>
                        <span class="center-name">${centro?.nome || 'Centro não encontrado'}</span>
                    </td>
                    <td>
                        <span class="badge status-${inscricao.status}">${inscricao.status}</span>
                    </td>
                    <td>
                        <span class="date">${AdminUtils.formatDate(inscricao.created_at)}</span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action btn-view" onclick="viewItem('pre-inscricao', ${inscricao.id})" title="Ver detalhes">
                                <i class="fas fa-eye"></i>
                                <span>Detalhes</span>
                            </button>
                            <div class="status-actions">
                                <button class="btn-action btn-success" onclick="updateStatus(${inscricao.id}, 'confirmado')" title="Confirmar">
                                    <i class="fas fa-check"></i>
                                    <span>Confirmar</span>
                                </button>
                                <button class="btn-action btn-danger" onclick="updateStatus(${inscricao.id}, 'cancelado')" title="Cancelar">
                                    <i class="fas fa-times"></i>
                                    <span>Cancelar</span>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        tbody.innerHTML = html;
        this.updateTableInfo('pre-inscricoes', this.data.preInscricoes.length);
    }

    updateTableInfo(type, total) {
        const showingElement = document.getElementById(`${type}-showing`);
        const totalElement = document.getElementById(`${type}-total`);
        
        if (showingElement) showingElement.textContent = total;
        if (totalElement) totalElement.textContent = total;
    }

    renderCalendar() {
        const calendarGrid = document.getElementById('calendar-grid');
        if (!calendarGrid || !this.data.horarios) return;

        // Definir dias da semana e períodos
        const diasSemana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
        const periodos = ['manhã', 'tarde', 'noite'];

        // Criar estrutura do calendário
        let calendarHTML = `
            <div class="calendar-header">
                <div class="time-slot-header">Período</div>
                ${diasSemana.map(dia => `<div class="day-header">${dia}</div>`).join('')}
            </div>
        `;

        // Criar linhas para cada período
        periodos.forEach(periodo => {
            calendarHTML += `<div class="calendar-row">`;
            calendarHTML += `<div class="time-slot">${periodo.charAt(0).toUpperCase() + periodo.slice(1)}</div>`;
            
            diasSemana.forEach(dia => {
                const horariosNoDia = this.data.horarios.filter(h => 
                    h.dia_semana === dia && h.periodo === periodo
                );
                
                calendarHTML += `<div class="day-cell" data-dia="${dia}" data-periodo="${periodo}">`;
                
                horariosNoDia.forEach(horario => {
                    const curso = this.data.cursos?.find(c => c.id === horario.curso_id);
                    const centro = this.data.centros?.find(c => c.id === curso?.centro_id);
                    
                    calendarHTML += `
                        <div class="calendar-event" onclick="viewCalendarEvent(${horario.id})">
                            <div class="event-title">${curso?.nome || 'Curso não encontrado'}</div>
                            <div class="event-time">${horario.hora_inicio} - ${horario.hora_fim}</div>
                            <div class="event-center">${centro?.nome || 'Centro não encontrado'}</div>
                        </div>
                    `;
                });
                
                calendarHTML += `</div>`;
            });
            
            calendarHTML += `</div>`;
        });

        calendarGrid.innerHTML = calendarHTML;
    }
}

// Controlador de navegação
class AdminNavigationController {
    constructor() {
        this.setupEventListeners();
        this.setupSearchAndFilters();
    }

    setupEventListeners() {
        // Navegação
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const section = item.dataset.section;
                this.showSection(section);
            });
        });

        // Mobile menu
        const mobileToggle = document.getElementById('mobile-menu-toggle');
        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                document.body.classList.toggle('sidebar-open');
            });
        }

        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                document.body.classList.toggle('sidebar-collapsed');
            });
        }
    }

    setupSearchAndFilters() {
        // Setup search inputs with debounce
        const searchInputs = [
            'centros-search',
            'cursos-search', 
            'formadores-search',
            'horarios-search',
            'pre-inscricoes-search'
        ];

        searchInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                const debouncedSearch = AdminUtils.debounce((e) => {
                    this.performAdvancedSearch(inputId.replace('-search', ''), e.target.value);
                }, ADMIN_CONFIG.DEBOUNCE_DELAY);
                
                input.addEventListener('input', debouncedSearch);
            }
        });

        // Setup filter selects
        const filterSelects = [
            'cursos-center-filter',
            'cursos-status-filter',
            'horarios-course-filter',
            'pre-inscricoes-course-filter',
            'pre-inscricoes-center-filter'
        ];

        filterSelects.forEach(selectId => {
            const select = document.getElementById(selectId);
            if (select) {
                select.addEventListener('change', (e) => {
                    this.performFilter(selectId, e.target.value);
                });
            }
        });

        // Setup status filters for pre-inscricoes
        document.querySelectorAll('.status-filter').forEach(button => {
            button.addEventListener('click', (e) => {
                document.querySelectorAll('.status-filter').forEach(btn => btn.classList.remove('active'));
                e.target.classList.add('active');
                this.performStatusFilter(e.target.dataset.status);
            });
        });
    }

    performSearch(section, searchTerm) {
        const tableBody = document.querySelector(`#${section}-table tbody`);
        if (!tableBody) return;

        const rows = tableBody.querySelectorAll('tr');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm.toLowerCase());
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        // Update table info
        dataController.updateTableInfo(section, visibleCount);
    }

    async performAdvancedSearch(section, searchTerm) {
        try {
            AdminUI.showLoading();
            
            // Construir parâmetros de busca
            const params = new URLSearchParams();
            if (searchTerm) {
                params.append('busca', searchTerm);
            }

            // Adicionar filtros ativos
            const activeFilters = this.getActiveFilters(section);
            Object.entries(activeFilters).forEach(([key, value]) => {
                if (value) params.append(key, value);
            });

            // Fazer chamada para API com filtros
            const response = await fetch(`${ADMIN_CONFIG.API_BASE_URL}/${section}?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            const results = data.dados || data;

            // Atualizar dados no estado
            dataController.data[section] = results;
            adminState.setState(section, results);

            // Re-renderizar tabela
            dataController.updateCurrentSection();

            AdminUI.hideLoading();
        } catch (error) {
            console.error('Erro na busca avançada:', error);
            AdminUI.hideLoading();
            AdminUI.showNotification('Erro ao realizar busca', 'error');
        }
    }

    getActiveFilters(section) {
        const filters = {};
        
        // Filtros específicos por seção
        switch (section) {
            case 'cursos':
                const centroFilter = document.getElementById('cursos-center-filter');
                const statusFilter = document.getElementById('cursos-status-filter');
                const areaFilter = document.getElementById('cursos-area-filter');
                const modalidadeFilter = document.getElementById('cursos-modalidade-filter');
                
                if (centroFilter && centroFilter.value) filters.centro_id = centroFilter.value;
                if (statusFilter && statusFilter.value) filters.ativo = statusFilter.value;
                if (areaFilter && areaFilter.value) filters.area = areaFilter.value;
                if (modalidadeFilter && modalidadeFilter.value) filters.modalidade = modalidadeFilter.value;
                break;
                
            case 'formadores':
                const formadorCentroFilter = document.getElementById('formadores-centro-filter');
                const especialidadeFilter = document.getElementById('formadores-especialidade-filter');
                
                if (formadorCentroFilter && formadorCentroFilter.value) filters.centro_id = formadorCentroFilter.value;
                if (especialidadeFilter && especialidadeFilter.value) filters.especialidade = especialidadeFilter.value;
                break;
                
            case 'pre-inscricoes':
                const preInscCursoFilter = document.getElementById('pre-inscricoes-course-filter');
                const preInscCentroFilter = document.getElementById('pre-inscricoes-center-filter');
                const preInscStatusFilter = document.querySelector('.status-filter.active');
                
                if (preInscCursoFilter && preInscCursoFilter.value) filters.curso_id = preInscCursoFilter.value;
                if (preInscCentroFilter && preInscCentroFilter.value) filters.centro_id = preInscCentroFilter.value;
                if (preInscStatusFilter && preInscStatusFilter.dataset.status) {
                    filters.status = preInscStatusFilter.dataset.status;
                }
                break;
        }
        
        return filters;
    }

    performFilter(filterId, filterValue) {
        const [section, filterType] = filterId.split('-');
        const tableBody = document.querySelector(`#${section}-table tbody`);
        if (!tableBody) return;

        const rows = tableBody.querySelectorAll('tr');
        let visibleCount = 0;

        rows.forEach(row => {
            let isVisible = true;

            if (filterValue) {
                if (filterType === 'center') {
                    const centerCell = row.cells[1]; // Assuming center is in second column
                    isVisible = centerCell && centerCell.textContent.includes(filterValue);
                } else if (filterType === 'status') {
                    const statusCell = row.querySelector('.badge');
                    if (filterValue === '1') {
                        isVisible = statusCell && statusCell.classList.contains('badge-success');
                    } else if (filterValue === '0') {
                        isVisible = statusCell && statusCell.classList.contains('badge-danger');
                    }
                } else if (filterType === 'course') {
                    const courseCell = row.cells[0]; // Assuming course is in first column
                    isVisible = courseCell && courseCell.textContent.includes(filterValue);
                }
            }

            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        dataController.updateTableInfo(section, visibleCount);
    }

    performStatusFilter(status) {
        const tableBody = document.querySelector('#pre-inscricoes-table tbody');
        if (!tableBody) return;

        const rows = tableBody.querySelectorAll('tr');
        let visibleCount = 0;

        rows.forEach(row => {
            const statusBadge = row.querySelector('.badge');
            let isVisible = true;

            if (status && statusBadge) {
                isVisible = statusBadge.classList.contains(`status-${status}`);
            }

            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        dataController.updateTableInfo('pre-inscricoes', visibleCount);
    }

    populateFilterOptions() {
        // Populate center filters
        const centerFilters = [
            document.getElementById('cursos-center-filter'),
            document.getElementById('pre-inscricoes-center-filter')
        ];

        centerFilters.forEach(select => {
            if (select && dataController.data.centros) {
                select.innerHTML = '<option value="">Todos os Centros</option>';
                dataController.data.centros.forEach(centro => {
                    const option = document.createElement('option');
                    option.value = centro.nome;
                    option.textContent = centro.nome;
                    select.appendChild(option);
                });
            }
        });

        // Populate course filters
        const courseFilters = [
            document.getElementById('horarios-course-filter'),
            document.getElementById('pre-inscricoes-course-filter')
        ];

        courseFilters.forEach(select => {
            if (select && dataController.data.cursos) {
                select.innerHTML = '<option value="">Todos os Cursos</option>';
                dataController.data.cursos.forEach(curso => {
                    const option = document.createElement('option');
                    option.value = curso.nome;
                    option.textContent = curso.nome;
                    select.appendChild(option);
                });
            }
        });
    }

    showSection(sectionName) {
        // Remove active from all nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });

        // Add active to current nav item
        const currentNavItem = document.querySelector(`[data-section="${sectionName}"]`);
        if (currentNavItem) {
            currentNavItem.classList.add('active');
        }

        // Hide all sections
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('active');
        });

        // Show current section
        const currentSection = document.getElementById(`${sectionName}-section`);
        if (currentSection) {
            currentSection.classList.add('active');
        }

        // Update page title
        const titles = {
            dashboard: 'Dashboard',
            centros: 'Gestão de Centros',
            cursos: 'Gestão de Cursos',
            formadores: 'Gestão de Formadores',
            horarios: 'Gestão de Horários',
            'pre-inscricoes': 'Gestão de Pré-inscrições',
            calendario: 'Calendário de Horários'
        };

        const pageTitle = document.getElementById('page-title');
        if (pageTitle) {
            pageTitle.textContent = titles[sectionName] || sectionName;
        }

        // Update state
        adminState.ui.currentSection = sectionName;

        // Load section data
        if (sectionName !== 'dashboard') {
            dataController.updateCurrentSection();
        }
    }
}

// Controlador de Modal
class AdminModalController {
    constructor() {
        this.modal = document.getElementById('universal-modal');
        this.modalTitle = document.getElementById('modal-title');
        this.modalForm = document.getElementById('modal-form');
        this.formFields = document.getElementById('form-fields');
        this.currentType = null;
        this.currentId = null;
        this.isEditMode = false;
    }

    open(type, id = null) {
        this.currentType = type;
        this.currentId = id;
        this.isEditMode = !!id;
        
        this.setupModal();
        this.modal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    close() {
        this.modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        this.modalForm.reset();
        this.currentType = null;
        this.currentId = null;
        this.isEditMode = false;
    }

    setupModal() {
        const titles = {
            centro: this.isEditMode ? 'Editar Centro' : 'Novo Centro',
            curso: this.isEditMode ? 'Editar Curso' : 'Novo Curso',
            formador: this.isEditMode ? 'Editar Formador' : 'Novo Formador',
            horario: this.isEditMode ? 'Editar Horário' : 'Novo Horário'
        };

        this.modalTitle.textContent = titles[this.currentType] || 'Modal';
        this.generateFormFields();
        
        if (this.isEditMode) {
            this.loadItemData();
        }
    }

    generateFormFields() {
        const fieldConfigs = {
            centro: [
                { name: 'nome', label: 'Nome', type: 'text', required: true },
                { name: 'localizacao', label: 'Localização', type: 'text', required: true },
                { name: 'email', label: 'Email', type: 'email', required: true },
                { name: 'contactos', label: 'Contactos (separados por vírgula)', type: 'text', required: true }
            ],
            curso: [
                { name: 'centro_id', label: 'Centro Principal', type: 'select', required: true, options: 'centros' },
                { name: 'centros', label: 'Centros Adicionais', type: 'multiselect', required: false, options: 'centros' },
                { name: 'nome', label: 'Nome do Curso', type: 'text', required: true },
                { name: 'descricao', label: 'Descrição', type: 'textarea', required: true },
                { name: 'programa', label: 'Programa do Curso', type: 'textarea', required: true },
                { name: 'area', label: 'Área', type: 'text', required: true },
                { name: 'duracao', label: 'Duração', type: 'text', required: true },
                { name: 'preco', label: 'Preço', type: 'number', required: true },
                { name: 'modalidade', label: 'Modalidade', type: 'select', required: true, 
                  options: [
                    { value: 'presencial', text: 'Presencial' },
                    { value: 'online', text: 'Online' },
                    { value: 'hibrido', text: 'Híbrido' }
                  ]
                },
                { name: 'ativo', label: 'Status', type: 'select', required: true,
                  options: [
                    { value: '1', text: 'Ativo' },
                    { value: '0', text: 'Inativo' }
                  ]
                }
            ],
            formador: [
                { name: 'nome', label: 'Nome', type: 'text', required: true },
                { name: 'email', label: 'Email', type: 'email', required: true },
                { name: 'especialidade', label: 'Especialidade', type: 'text', required: true },
                { name: 'contactos', label: 'Contactos (separados por vírgula)', type: 'text', required: true },
                { name: 'bio', label: 'Biografia', type: 'textarea', required: false },
                { name: 'cursos', label: 'Cursos', type: 'multiselect', required: false, options: 'cursos' },
                { name: 'centros', label: 'Centros', type: 'multiselect', required: false, options: 'centros' }
            ],
            horario: [
                { name: 'curso_id', label: 'Curso', type: 'select', required: true, options: 'cursos' },
                { name: 'dia_semana', label: 'Dia da Semana', type: 'select', required: true,
                  options: [
                    { value: 'Segunda', text: 'Segunda-feira' },
                    { value: 'Terça', text: 'Terça-feira' },
                    { value: 'Quarta', text: 'Quarta-feira' },
                    { value: 'Quinta', text: 'Quinta-feira' },
                    { value: 'Sexta', text: 'Sexta-feira' },
                    { value: 'Sábado', text: 'Sábado' },
                    { value: 'Domingo', text: 'Domingo' }
                  ]
                },
                { name: 'periodo', label: 'Período', type: 'select', required: true,
                  options: [
                    { value: 'manhã', text: 'Manhã' },
                    { value: 'tarde', text: 'Tarde' },
                    { value: 'noite', text: 'Noite' }
                  ]
                },
                { name: 'hora_inicio', label: 'Hora de Início', type: 'time', required: true },
                { name: 'hora_fim', label: 'Hora de Fim', type: 'time', required: true }
            ]
        };

        const fields = fieldConfigs[this.currentType] || [];
        
        this.formFields.innerHTML = fields.map(field => {
            return this.generateField(field);
        }).join('');

        // Setup form submission
        this.modalForm.onsubmit = (e) => {
            e.preventDefault();
            this.handleSubmit();
        };
    }

    generateField(field) {
        const { name, label, type, required, options } = field;
        
        let inputHtml = '';
        
        if (type === 'select') {
            if (typeof options === 'string') {
                // Options from data (centros, cursos, etc.)
                const dataOptions = dataController.data[options] || [];
                const optionsHtml = dataOptions.map(item => 
                    `<option value="${item.id}">${item.nome}</option>`
                ).join('');
                inputHtml = `<select name="${name}" id="${name}" ${required ? 'required' : ''}>
                    <option value="">Selecione...</option>
                    ${optionsHtml}
                </select>`;
            } else if (Array.isArray(options)) {
                // Static options
                const optionsHtml = options.map(opt => 
                    `<option value="${opt.value}">${opt.text}</option>`
                ).join('');
                inputHtml = `<select name="${name}" id="${name}" ${required ? 'required' : ''}>
                    <option value="">Selecione...</option>
                    ${optionsHtml}
                </select>`;
            }
        } else if (type === 'multiselect') {
            if (typeof options === 'string') {
                // Options from data (centros, cursos, etc.)
                const dataOptions = dataController.data[options] || [];
                const optionsHtml = dataOptions.map(item => 
                    `<option value="${item.id}">${item.nome}</option>`
                ).join('');
                inputHtml = `<select name="${name}" id="${name}" multiple class="multiselect" ${required ? 'required' : ''}>
                    ${optionsHtml}
                </select>
                <small class="help-text">Segure Ctrl/Cmd para selecionar múltiplos</small>`;
            } else if (Array.isArray(options)) {
                // Static options
                const optionsHtml = options.map(opt => 
                    `<option value="${opt.value}">${opt.text}</option>`
                ).join('');
                inputHtml = `<select name="${name}" id="${name}" multiple class="multiselect" ${required ? 'required' : ''}>
                    ${optionsHtml}
                </select>
                <small class="help-text">Segure Ctrl/Cmd para selecionar múltiplos</small>`;
            }
        } else if (type === 'textarea') {
            inputHtml = `<textarea name="${name}" id="${name}" ${required ? 'required' : ''}></textarea>`;
        } else {
            inputHtml = `<input type="${type}" name="${name}" id="${name}" ${required ? 'required' : ''}>`;
        }

        return `
            <div class="form-field">
                <label for="${name}">${label} ${required ? '*' : ''}</label>
                ${inputHtml}
            </div>
        `;
    }

    loadItemData() {
        if (!this.isEditMode || !this.currentId) return;
        
        const pluralType = this.currentType + 's';
        const item = dataController.data[pluralType]?.find(item => item.id == this.currentId);
        
        if (!item) return;

        // Populate form fields
        Object.keys(item).forEach(key => {
            const field = this.modalForm.querySelector(`[name="${key}"]`);
            if (field) {
                if (key === 'contactos' && Array.isArray(item[key])) {
                    field.value = item[key].join(', ');
                } else if (field.multiple && Array.isArray(item[key])) {
                    // Handle multiselect fields
                    Array.from(field.options).forEach(option => {
                        option.selected = item[key].some(selectedItem => 
                            selectedItem.id == option.value
                        );
                    });
                } else {
                    field.value = item[key] || '';
                }
            }
        });
    }

    async handleSubmit() {
        const formData = new FormData(this.modalForm);
        const data = {};
        
        // Convert FormData to object, handling multiple values
        const formDataEntries = {};
        for (let [key, value] of formData.entries()) {
            if (formDataEntries[key]) {
                // If key already exists, convert to array or add to existing array
                if (Array.isArray(formDataEntries[key])) {
                    formDataEntries[key].push(value);
                } else {
                    formDataEntries[key] = [formDataEntries[key], value];
                }
            } else {
                formDataEntries[key] = value;
            }
        }

        // Process the form data
        for (let [key, value] of Object.entries(formDataEntries)) {
            if (key === 'contactos') {
                // Convert comma-separated string to array
                data[key] = value.split(',').map(contact => contact.trim()).filter(contact => contact);
            } else if (key === 'cursos' || key === 'centros') {
                // Handle multiselect values
                data[key] = Array.isArray(value) ? value.map(v => parseInt(v)) : [parseInt(value)];
            } else if (key === 'ativo') {
                data[key] = parseInt(value);
            } else if (key.includes('_id')) {
                data[key] = parseInt(value) || null;
            } else if (key === 'preco') {
                data[key] = parseFloat(value) || 0;
            } else {
                data[key] = value;
            }
        }

        try {
            AdminUI.showLoading();
            
            const pluralType = this.currentType + 's';
            let result;
            
            if (this.isEditMode) {
                result = await adminApi.update(pluralType, this.currentId, data);
                AdminUI.showNotification(`${this.currentType} atualizado com sucesso!`, 'success');
            } else {
                result = await adminApi.create(pluralType, data);
                AdminUI.showNotification(`${this.currentType} criado com sucesso!`, 'success');
            }
            
            this.close();
            await dataController.loadAllData();
            
        } catch (error) {
            console.error('Erro ao submeter formulário:', error);
            AdminUI.showNotification('Erro ao salvar dados', 'error');
        } finally {
            AdminUI.hideLoading();
        }
    }
}

const modalController = new AdminModalController();

// Funções globais
window.openCreateModal = (type) => {
    modalController.open(type);
};

window.closeModal = () => {
    modalController.close();
};

window.viewItem = async (type, id) => {
    try {
        AdminUI.showLoading();
        
        // Fetch detailed data from API for more complete information
        const detailedItem = await adminApi.getById(type + 's', id);
        
        if (!detailedItem) {
            AdminUI.showNotification('Item não encontrado', 'error');
            return;
        }

        // Create a detailed view modal
        const modal = document.createElement('div');
        modal.className = 'modal view-modal';
        modal.innerHTML = `
            <div class="modal-overlay" onclick="this.closest('.modal').remove()"></div>
            <div class="modal-container">
                <div class="modal-header">
                    <h2>Detalhes do ${type.charAt(0).toUpperCase() + type.slice(1)}</h2>
                    <button class="modal-close" onclick="this.closest('.modal').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-content">
                    <div class="view-details">
                        ${generateViewDetails(type, detailedItem)}
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        modal.classList.add('show');
        
    } catch (error) {
        console.error('Erro ao carregar detalhes:', error);
        AdminUI.showNotification('Erro ao carregar detalhes do item', 'error');
    } finally {
        AdminUI.hideLoading();
    }
};

window.editItem = (type, id) => {
    modalController.open(type, id);
};

function generateViewDetails(type, item) {
    let detailsHtml = '';
    
    // Basic information section
    detailsHtml += '<div class="details-section"><h3>Informações Básicas</h3>';
    
    const commonFields = {
        centro: ['nome', 'localizacao', 'email', 'contactos'],
        curso: ['nome', 'descricao', 'area', 'duracao', 'preco', 'modalidade', 'programa', 'ativo'],
        formador: ['nome', 'email', 'especialidade', 'contactos', 'bio'],
        horario: ['dia_semana', 'periodo', 'hora_inicio', 'hora_fim'],
        'pre-inscricao': ['nome_completo', 'email', 'contactos', 'status', 'observacoes']
    };

    const fields = commonFields[type] || Object.keys(item).filter(key => 
        !['id', 'created_at', 'updated_at', 'cursos', 'formadores', 'centros', 'centro', 'curso'].includes(key)
    );
    
    detailsHtml += fields.map(field => {
        let value = item[field];
        let label = field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        
        if (field === 'contactos' && Array.isArray(value)) {
            value = AdminUtils.formatContactsDisplay(value);
        } else if (field === 'preco') {
            value = AdminUtils.formatCurrency(value);
        } else if (field === 'ativo') {
            value = value ? '<span class="badge badge-success">Ativo</span>' : '<span class="badge badge-danger">Inativo</span>';
        } else if (field === 'status') {
            value = `<span class="badge status-${value}">${value}</span>`;
        } else if (field === 'created_at' || field === 'updated_at') {
            value = AdminUtils.formatDate(value);
        }
        
        return `
            <div class="detail-row">
                <strong>${label}:</strong>
                <span>${value || 'N/A'}</span>
            </div>
        `;
    }).join('');
    
    detailsHtml += '</div>';
    
    // Related information based on type
    if (type === 'centro') {
        // Show courses from this center
        if (item.cursos && item.cursos.length > 0) {
            detailsHtml += '<div class="details-section"><h3>Cursos Oferecidos</h3>';
            detailsHtml += '<div class="related-items">';
            item.cursos.forEach(curso => {
                detailsHtml += `
                    <div class="related-item">
                        <div class="related-item-header">
                            <i class="fas fa-graduation-cap"></i>
                            <strong>${curso.nome}</strong>
                            <span class="badge ${curso.ativo ? 'badge-success' : 'badge-danger'}">
                                ${curso.ativo ? 'Ativo' : 'Inativo'}
                            </span>
                        </div>
                        <div class="related-item-details">
                            <p><strong>Área:</strong> ${curso.area}</p>
                            <p><strong>Duração:</strong> ${curso.duracao}</p>
                            <p><strong>Preço:</strong> ${AdminUtils.formatCurrency(curso.preco)}</p>
                            <p><strong>Modalidade:</strong> ${curso.modalidade}</p>
                        </div>
                    </div>
                `;
            });
            detailsHtml += '</div></div>';
        }
        
        // Show trainers from this center
        if (item.formadores && item.formadores.length > 0) {
            detailsHtml += '<div class="details-section"><h3>Formadores</h3>';
            detailsHtml += '<div class="related-items">';
            item.formadores.forEach(formador => {
                detailsHtml += `
                    <div class="related-item">
                        <div class="related-item-header">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <strong>${formador.nome}</strong>
                        </div>
                        <div class="related-item-details">
                            <p><strong>Email:</strong> ${formador.email}</p>
                            <p><strong>Especialidade:</strong> ${formador.especialidade}</p>
                            <p><strong>Contactos:</strong> ${AdminUtils.formatContactsDisplay(formador.contactos)}</p>
                        </div>
                    </div>
                `;
            });
            detailsHtml += '</div></div>';
        }
    } else if (type === 'curso') {
        // Show center information
        if (item.centro) {
            detailsHtml += '<div class="details-section"><h3>Centro Principal</h3>';
            detailsHtml += `
                <div class="related-item">
                    <div class="related-item-header">
                        <i class="fas fa-building"></i>
                        <strong>${item.centro.nome}</strong>
                        <span class="badge badge-primary">Principal</span>
                    </div>
                    <div class="related-item-details">
                        <p><strong>Localização:</strong> ${item.centro.localizacao}</p>
                        <p><strong>Email:</strong> ${item.centro.email}</p>
                        <p><strong>Contactos:</strong> ${AdminUtils.formatContactsDisplay(item.centro.contactos)}</p>
                    </div>
                </div>
            `;
            detailsHtml += '</div>';
        }

        // Show additional centers
        if (item.centros && item.centros.length > 0) {
            detailsHtml += '<div class="details-section"><h3>Centros Adicionais</h3>';
            detailsHtml += '<div class="related-items">';
            item.centros.forEach(centro => {
                detailsHtml += `
                    <div class="related-item">
                        <div class="related-item-header">
                            <i class="fas fa-building"></i>
                            <strong>${centro.nome}</strong>
                            <span class="badge badge-info">Adicional</span>
                        </div>
                        <div class="related-item-details">
                            <p><strong>Localização:</strong> ${centro.localizacao}</p>
                            <p><strong>Email:</strong> ${centro.email}</p>
                            <p><strong>Contactos:</strong> ${AdminUtils.formatContactsDisplay(centro.contactos)}</p>
                        </div>
                    </div>
                `;
            });
            detailsHtml += '</div></div>';
        }
        
        // Show trainers for this course
        if (item.formadores && item.formadores.length > 0) {
            detailsHtml += '<div class="details-section"><h3>Formadores</h3>';
            detailsHtml += '<div class="related-items">';
            item.formadores.forEach(formador => {
                detailsHtml += `
                    <div class="related-item">
                        <div class="related-item-header">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <strong>${formador.nome}</strong>
                        </div>
                        <div class="related-item-details">
                            <p><strong>Email:</strong> ${formador.email}</p>
                            <p><strong>Especialidade:</strong> ${formador.especialidade}</p>
                        </div>
                    </div>
                `;
            });
            detailsHtml += '</div></div>';
        }
    } else if (type === 'formador') {
        // Show courses and centers
        if (item.cursos && item.cursos.length > 0) {
            detailsHtml += '<div class="details-section"><h3>Cursos</h3>';
            detailsHtml += '<div class="related-items">';
            item.cursos.forEach(curso => {
                detailsHtml += `
                    <div class="related-item">
                        <div class="related-item-header">
                            <i class="fas fa-graduation-cap"></i>
                            <strong>${curso.nome}</strong>
                        </div>
                        <div class="related-item-details">
                            <p><strong>Área:</strong> ${curso.area}</p>
                            <p><strong>Modalidade:</strong> ${curso.modalidade}</p>
                        </div>
                    </div>
                `;
            });
            detailsHtml += '</div></div>';
        }
        
        if (item.centros && item.centros.length > 0) {
            detailsHtml += '<div class="details-section"><h3>Centros</h3>';
            detailsHtml += '<div class="related-items">';
            item.centros.forEach(centro => {
                detailsHtml += `
                    <div class="related-item">
                        <div class="related-item-header">
                            <i class="fas fa-building"></i>
                            <strong>${centro.nome}</strong>
                        </div>
                        <div class="related-item-details">
                            <p><strong>Localização:</strong> ${centro.localizacao}</p>
                        </div>
                    </div>
                `;
            });
            detailsHtml += '</div></div>';
        }
    } else if (type === 'horario') {
        // Show course information
        if (item.curso) {
            detailsHtml += '<div class="details-section"><h3>Curso</h3>';
            detailsHtml += `
                <div class="related-item">
                    <div class="related-item-header">
                        <i class="fas fa-graduation-cap"></i>
                        <strong>${item.curso.nome}</strong>
                    </div>
                    <div class="related-item-details">
                        <p><strong>Centro:</strong> ${item.curso.centro?.nome || 'N/A'}</p>
                        <p><strong>Área:</strong> ${item.curso.area}</p>
                        <p><strong>Duração:</strong> ${item.curso.duracao}</p>
                    </div>
                </div>
            `;
            detailsHtml += '</div>';
        }
    }
    
    // Show timestamps
    detailsHtml += '<div class="details-section"><h3>Informações do Sistema</h3>';
    detailsHtml += `
        <div class="detail-row">
            <strong>Criado em:</strong>
            <span>${AdminUtils.formatDate(item.created_at)}</span>
        </div>
        <div class="detail-row">
            <strong>Atualizado em:</strong>
            <span>${AdminUtils.formatDate(item.updated_at)}</span>
        </div>
    `;
    detailsHtml += '</div>';
    
    return detailsHtml;
}

window.deleteItem = (type, id) => {
    const item = dataController.data[type + 's']?.find(item => item.id === id);
    if (!item) return;

    AdminUI.showConfirmModal(
        'Confirmar Exclusão',
        `Tem certeza que deseja excluir este ${type}?`,
        async () => {
            try {
                await adminApi.delete(type + 's', id);
                AdminUI.showNotification('Item excluído com sucesso!', 'success');
                await dataController.loadAllData();
            } catch (error) {
                AdminUI.showNotification('Erro ao excluir item', 'error');
            }
        }
    );
};

window.updateStatus = async (id, status) => {
    try {
        await adminApi.update('pre-inscricoes', id, { status });
        AdminUI.showNotification('Status atualizado com sucesso!', 'success');
        await dataController.loadAllData();
    } catch (error) {
        AdminUI.showNotification('Erro ao atualizar status', 'error');
    }
};

window.refreshChart = (chartType) => {
    dataController.renderCharts();
};

window.reloadAllData = async () => {
    AdminUI.showNotification('Recarregando dados...', 'info');
    await dataController.loadAllData();
};

window.refreshCalendar = () => {
    if (dataController) {
        dataController.renderCalendar();        
        AdminUI.showNotification('Calendário atualizado!', 'success');
    }
};

window.viewCalendarEvent = (horarioId) => {
    window.viewItem('horario', horarioId);
};

window.showAllContacts = (contacts) => {
    const contactsList = contacts.map(contact => 
        `<div class="contact-item">${AdminUtils.formatPhoneNumber(contact)}</div>`
    ).join('');
    
    AdminUI.showNotification(`
        <div class="contacts-modal">
            <h4>Todos os contactos:</h4>
            ${contactsList}
        </div>
    `, 'info', 8000);
};

// Função para aplicar validações em tempo real
window.setupFormValidation = (formId) => {
    const form = document.getElementById(formId);
    if (!form) return;

    // Adiciona validação em tempo real para campos específicos
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        const fieldName = input.name || input.id;
        
        // Aplicar formatação automática
        if (fieldName === 'contactos' || fieldName.includes('telefone')) {
            input.addEventListener('input', (e) => {
                const formatted = AdminValidation.formatPhoneInput(e.target.value);
                if (formatted !== e.target.value) {
                    e.target.value = formatted;
                }
                
                // Validação visual
                const isValid = AdminValidation.validatePhone(formatted);
                toggleFieldValidation(e.target, isValid);
            });
        }
        
        if (fieldName === 'email') {
            input.addEventListener('blur', (e) => {
                const isValid = AdminValidation.validateEmail(e.target.value);
                toggleFieldValidation(e.target, isValid);
            });
        }
        
        if (fieldName === 'nome' || fieldName.includes('nome')) {
            input.addEventListener('blur', (e) => {
                const isValid = AdminValidation.validateName(e.target.value);
                toggleFieldValidation(e.target, isValid);
            });
        }
        
        if (fieldName === 'preco') {
            input.addEventListener('input', (e) => {
                // Permite apenas números e ponto decimal
                e.target.value = e.target.value.replace(/[^0-9.]/g, '');
                
                const isValid = AdminValidation.validatePrice(e.target.value);
                toggleFieldValidation(e.target, isValid);
            });
        }
        
        if (fieldName === 'duracao') {
            input.addEventListener('input', (e) => {
                // Permite apenas números
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                
                const isValid = AdminValidation.validateDuration(e.target.value);
                toggleFieldValidation(e.target, isValid);
            });
        }
    });
};

// Função para mostrar/esconder indicadores de validação
function toggleFieldValidation(field, isValid) {
    const fieldContainer = field.closest('.form-field') || field.parentElement;
    
    // Remove classes existentes
    fieldContainer.classList.remove('field-valid', 'field-invalid');
    
    // Remove mensagens de erro existentes
    const existingError = fieldContainer.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    
    if (field.value.trim() === '') {
        return; // Não mostrar validação para campos vazios
    }
    
    if (isValid) {
        fieldContainer.classList.add('field-valid');
    } else {
        fieldContainer.classList.add('field-invalid');
        
        // Adiciona mensagem de erro
        const errorMsg = document.createElement('div');
        errorMsg.className = 'field-error';
        errorMsg.textContent = AdminValidation.getErrorMessage(field.name || field.id);
        fieldContainer.appendChild(errorMsg);
    }
}

// Inicialização da aplicação
let dataController;
let navigationController;

document.addEventListener('DOMContentLoaded', async () => {
    console.log('🚀 FormAcademy Admin - Iniciando sistema melhorado...');

    try {
        // Inicializa controladores
        dataController = new AdminDataController();
        navigationController = new AdminNavigationController();

        // Carrega todos os dados imediatamente
        await dataController.loadAllData();

        // Sistema de notificações (verifica a cada 30 segundos)
        setInterval(() => {
            this.checkForNewNotifications();
        }, 30000);

        // Inicializa AOS se disponível
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 600,
                easing: 'ease-out',
                once: true,
                offset: 50
            });
        }

        AdminUI.showNotification('Sistema carregado com sucesso!', 'success');
        console.log('✅ FormAcademy Admin - Sistema inicializado com sucesso!');

    } catch (error) {
        console.error('❌ Erro ao inicializar admin:', error);
        AdminUI.showNotification('Erro ao carregar o sistema', 'error');
    }
});

// Sistema de notificações
async function checkForNewNotifications() {
    try {
        // Verificar novas pré-inscrições
        const response = await fetch(`${ADMIN_CONFIG.API_BASE_URL}/pre-inscricoes?data_inicio=${new Date(adminState.lastDataCheck).toISOString()}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            const data = await response.json();
            const novasInscricoes = data.dados || [];

            if (novasInscricoes.length > 0) {
                // Mostrar notificação para novas inscrições
                AdminUI.showNotification(
                    `${novasInscricoes.length} nova(s) pré-inscrição(ões) recebida(s)!`, 
                    'info', 
                    8000
                );

                // Atualizar badge no menu (se existir)
                updateNotificationBadge('pre-inscricoes', novasInscricoes.length);

                // Reproduzir som de notificação (opcional)
                playNotificationSound();
            }
        }

        adminState.lastDataCheck = Date.now();
    } catch (error) {
        console.error('Erro ao verificar notificações:', error);
    }
}

function updateNotificationBadge(section, count) {
    const navItem = document.querySelector(`[data-section="${section}"]`);
    if (navItem) {
        let badge = navItem.querySelector('.notification-badge');
        if (!badge && count > 0) {
            badge = document.createElement('span');
            badge.className = 'notification-badge';
            navItem.appendChild(badge);
        }
        
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
        }
    }
}

function playNotificationSound() {
    // Criar um som de notificação simples usando Web Audio API
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
        
        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2);
    } catch (error) {
        console.log('Som de notificação não suportado');
    }
}

// Expor função globalmente
window.checkForNewNotifications = checkForNewNotifications;

// Sistema de Relatórios e Exportação
class AdminReportSystem {
    static exportData(type, format = 'csv') {
        const data = dataController.data[type];
        if (!data || data.length === 0) {
            AdminUI.showNotification('Não há dados para exportar', 'warning');
            return;
        }

        switch (format.toLowerCase()) {
            case 'csv':
                this.exportToCSV(type, data);
                break;
            case 'json':
                this.exportToJSON(type, data);
                break;
            default:
                AdminUI.showNotification('Formato não suportado', 'error');
        }
    }

    static exportToCSV(type, data) {
        try {
            // Definir colunas específicas para cada tipo
            const columnMappings = {
                centros: [
                    { key: 'nome', label: 'Nome' },
                    { key: 'localizacao', label: 'Localização' },
                    { key: 'email', label: 'Email' },
                    { key: 'contactos', label: 'Contactos' },
                    { key: 'created_at', label: 'Data de Criação' }
                ],
                cursos: [
                    { key: 'nome', label: 'Nome' },
                    { key: 'area', label: 'Área' },
                    { key: 'duracao', label: 'Duração' },
                    { key: 'preco', label: 'Preço' },
                    { key: 'modalidade', label: 'Modalidade' },
                    { key: 'ativo', label: 'Status' },
                    { key: 'centro.nome', label: 'Centro' },
                    { key: 'created_at', label: 'Data de Criação' }
                ],
                formadores: [
                    { key: 'nome', label: 'Nome' },
                    { key: 'email', label: 'Email' },
                    { key: 'especialidade', label: 'Especialidade' },
                    { key: 'contactos', label: 'Contactos' },
                    { key: 'created_at', label: 'Data de Criação' }
                ],
                horarios: [
                    { key: 'curso.nome', label: 'Curso' },
                    { key: 'dia_semana', label: 'Dia da Semana' },
                    { key: 'periodo', label: 'Período' },
                    { key: 'hora_inicio', label: 'Hora Início' },
                    { key: 'hora_fim', label: 'Hora Fim' },
                    { key: 'created_at', label: 'Data de Criação' }
                ],
                preInscricoes: [
                    { key: 'nome_completo', label: 'Nome' },
                    { key: 'email', label: 'Email' },
                    { key: 'contactos', label: 'Contactos' },
                    { key: 'curso.nome', label: 'Curso' },
                    { key: 'status', label: 'Status' },
                    { key: 'created_at', label: 'Data de Inscrição' }
                ]
            };

            const columns = columnMappings[type] || [];
            
            // Criar cabeçalho CSV
            const headers = columns.map(col => `"${col.label}"`).join(',');
            
            // Criar linhas CSV
            const rows = data.map(item => {
                return columns.map(col => {
                    let value = this.getNestedValue(item, col.key);
                    
                    // Formatação especial para alguns tipos de dados
                    if (col.key === 'contactos' && Array.isArray(value)) {
                        value = value.join('; ');
                    } else if (col.key === 'preco') {
                        value = AdminUtils.formatCurrency(value);
                    } else if (col.key === 'ativo') {
                        value = value ? 'Ativo' : 'Inativo';
                    } else if (col.key.includes('created_at') || col.key.includes('updated_at')) {
                        value = AdminUtils.formatDate(value);
                    }
                    
                    // Escapar aspas e envolver em aspas
                    return `"${String(value || '').replace(/"/g, '""')}"`;
                }).join(',');
            });

            // Combinar cabeçalho e linhas
            const csvContent = [headers, ...rows].join('\n');
            
            // Fazer download
            this.downloadFile(csvContent, `${type}_${new Date().toISOString().split('T')[0]}.csv`, 'text/csv');
            
            AdminUI.showNotification(`Dados de ${type} exportados com sucesso!`, 'success');
        } catch (error) {
            console.error('Erro ao exportar CSV:', error);
            AdminUI.showNotification('Erro ao exportar dados', 'error');
        }
    }

    static exportToJSON(type, data) {
        try {
            const jsonContent = JSON.stringify(data, null, 2);
            this.downloadFile(jsonContent, `${type}_${new Date().toISOString().split('T')[0]}.json`, 'application/json');
            AdminUI.showNotification(`Dados de ${type} exportados em JSON!`, 'success');
        } catch (error) {
            console.error('Erro ao exportar JSON:', error);
            AdminUI.showNotification('Erro ao exportar dados', 'error');
        }
    }

    static getNestedValue(obj, path) {
        return path.split('.').reduce((current, key) => current?.[key], obj);
    }

    static downloadFile(content, fileName, mimeType) {
        const blob = new Blob([content], { type: mimeType });
        const url = URL.createObjectURL(blob);
        
        const link = document.createElement('a');
        link.href = url;
        link.download = fileName;
        link.style.display = 'none';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        URL.revokeObjectURL(url);
    }

    static generateReport(type) {
        const data = dataController.data[type];
        if (!data || data.length === 0) {
            AdminUI.showNotification('Não há dados para o relatório', 'warning');
            return;
        }

        // Criar relatório em HTML
        const reportContent = this.generateHTMLReport(type, data);
        
        // Abrir em nova janela para impressão
        const printWindow = window.open('', '_blank');
        printWindow.document.write(reportContent);
        printWindow.document.close();
        printWindow.focus();
        
        setTimeout(() => {
            printWindow.print();
        }, 500);
    }

    static generateHTMLReport(type, data) {
        const reportTitle = {
            centros: 'Relatório de Centros',
            cursos: 'Relatório de Cursos', 
            formadores: 'Relatório de Formadores',
            horarios: 'Relatório de Horários',
            preInscricoes: 'Relatório de Pré-inscrições'
        };

        return `
            <!DOCTYPE html>
            <html>
            <head>
                <title>${reportTitle[type]}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h1 { color: #3b82f6; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f8fafc; font-weight: bold; }
                    .report-info { background: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
                    @media print { .no-print { display: none; } }
                </style>
            </head>
            <body>
                <h1>${reportTitle[type]}</h1>
                <div class="report-info">
                    <p><strong>Data do Relatório:</strong> ${new Date().toLocaleDateString('pt-BR')}</p>
                    <p><strong>Total de Registros:</strong> ${data.length}</p>
                </div>
                ${this.generateReportTable(type, data)}
            </body>
            </html>
        `;
    }

    static generateReportTable(type, data) {
        // Implementação básica - pode ser expandida
        const firstItem = data[0];
        const headers = Object.keys(firstItem).filter(key => 
            !['id', 'updated_at', 'pivot'].includes(key)
        );

        const headerRow = headers.map(header => 
            `<th>${header.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</th>`
        ).join('');

        const dataRows = data.map(item => {
            const cells = headers.map(header => {
                let value = item[header];
                if (Array.isArray(value)) value = value.join(', ');
                if (typeof value === 'object' && value !== null) value = JSON.stringify(value);
                return `<td>${value || ''}</td>`;
            }).join('');
            return `<tr>${cells}</tr>`;
        }).join('');

        return `
            <table>
                <thead><tr>${headerRow}</tr></thead>
                <tbody>${dataRows}</tbody>
            </table>
        `;
    }
}

// Funções globais para exportação
window.exportData = (type, format) => {
    AdminReportSystem.exportData(type, format);
};

window.generateReport = (type) => {
    AdminReportSystem.generateReport(type);
};
