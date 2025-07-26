// Modern Admin Script - FormAcademy
// =====================================

// Configuration
const ADMIN_CONFIG = {
    API_BASE_URL: '/api',
    ITEMS_PER_PAGE: 10,
    DEBOUNCE_DELAY: 300,
    MAX_RETRIES: 3,
    RETRY_DELAY: 1000
};

// Global State Management for Admin
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
    }

    // State management
    setState(key, value) {
        this.data[key] = value;
        this.notify(key, value);
    }

    getState(key) {
        return this.data[key];
    }

    setUIState(key, value) {
        this.ui[key] = value;
        this.notify(`ui.${key}`, value);
    }

    getUIState(key) {
        return this.ui[key];
    }

    // Observer pattern
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

// Admin Utility Functions
const AdminUtils = {
    // Format currency
    formatCurrency(amount) {
        return new Intl.NumberFormat('pt-AO', {
            style: 'currency',
            currency: 'AOA',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    },

    // Format date
    formatDate(dateString) {
        return new Intl.DateTimeFormat('pt-BR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(dateString));
    },

    // Sanitize HTML
    sanitizeHTML(str) {
        if (!str) return '';
        const temp = document.createElement('div');
        temp.textContent = str;
        return temp.innerHTML;
    },

    // Parse contacts
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
        } else if (typeof contacts === 'object' && contacts !== null) {
            return Object.values(contacts).filter(contact => contact && contact.trim());
        }
        return [];
    },

    // Format contacts for display
    formatContactsDisplay(contacts) {
        const contactsArray = this.parseContacts(contacts);
        if (contactsArray.length === 0) return 'Sem contacto';
        
        // Formatar primeiro telefone
        const firstContact = this.formatPhoneNumber(contactsArray[0]);
        
        if (contactsArray.length === 1) {
            return firstContact;
        } else {
            return `${firstContact} (+${contactsArray.length - 1})`;
        }
    },

    // Format phone number (Angola format: 9XX XXX XXX)
    formatPhoneNumber(phone) {
        if (!phone) return '';
        
        const cleanPhone = phone.replace(/\D/g, '');
        
        if (cleanPhone.length === 9 && cleanPhone.startsWith('9')) {
            return `${cleanPhone.slice(0, 3)} ${cleanPhone.slice(3, 6)} ${cleanPhone.slice(6)}`;
        }
        
        return phone;
    },

    // Debounce function
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

    // Generate pagination
    generatePagination(currentPage, totalPages, totalItems, itemsPerPage) {
        const startItem = (currentPage - 1) * itemsPerPage + 1;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);
        
        return {
            currentPage,
            totalPages,
            totalItems,
            startItem,
            endItem,
            hasNext: currentPage < totalPages,
            hasPrev: currentPage > 1
        };
    },

    // Animate counter
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

// Admin API Service
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
                ...options.headers
            },
            ...options
        };

        try {
            const response = await fetch(url, config);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            this.retryCount = 0;
            
            return data.dados || data;
        } catch (error) {
            console.error(`Admin API Error (${endpoint}):`, error);
            
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

// Admin UI Components
class AdminUIComponents {
    static showLoading(show = true) {
        const loading = document.getElementById('loading');
        if (loading) {
            if (show) {
                loading.classList.add('show');
                adminState.setUIState('loading', true);
            } else {
                loading.classList.remove('show');
                adminState.setUIState('loading', false);
            }
        }
    }

    static showNotification(message, type = 'success', duration = 5000) {
        const notification = document.getElementById('notification');
        if (!notification) return;

        const iconMap = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };

        notification.className = `notification ${type}`;
        notification.querySelector('.notification-icon').className = `notification-icon ${iconMap[type]}`;
        notification.querySelector('.notification-text').textContent = message;
        
        notification.classList.add('show');

        setTimeout(() => {
            notification.classList.remove('show');
        }, duration);
    }

    static closeNotification() {
        const notification = document.getElementById('notification');
        if (notification) {
            notification.classList.remove('show');
        }
    }

    static openModal(title, type, editId = null) {
        const modal = document.getElementById('universal-modal');
        const modalTitle = document.getElementById('modal-title');
        const formFields = document.getElementById('form-fields');
        
        if (!modal || !modalTitle || !formFields) return;

        modalTitle.textContent = title;
        adminState.setUIState('currentModal', type);
        adminState.setUIState('currentEditId', editId);
        adminState.setUIState('currentEditType', type);

        // Generate form fields based on type
        formFields.innerHTML = this.generateFormFields(type);
        
        // If editing, populate with existing data
        if (editId) {
            this.populateFormData(type, editId);
        }

        // Add contact field functionality
        this.setupContactFields();
        
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    static closeModal() {
        const modal = document.getElementById('universal-modal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
            adminState.setUIState('currentModal', null);
            adminState.setUIState('currentEditId', null);
            adminState.setUIState('currentEditType', null);
        }
    }

    static generateFormFields(type) {
        const centros = adminState.getState('centros');
        const cursos = adminState.getState('cursos');

        switch (type) {
            case 'centro':
                return `
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">Nome do Centro *</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label for="localizacao">Localização *</label>
                            <input type="text" id="localizacao" name="localizacao" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label>Contactos</label>
                        <div class="contact-group" id="contact-group">
                            <div class="contact-item">
                                <input type="tel" name="contacto" placeholder="Número de telefone">
                                <button type="button" class="btn-add-contact" onclick="AdminUIComponents.addContactField()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

            case 'curso':
                const centroOptions = centros.map(centro => 
                    `<option value="${centro.id}">${AdminUtils.sanitizeHTML(centro.nome)}</option>`
                ).join('');
                
                return `
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">Nome do Curso *</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label for="centro_id">Centro *</label>
                            <select id="centro_id" name="centro_id" required>
                                <option value="">Selecione um centro</option>
                                ${centroOptions}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="programa">Programa do Curso</label>
                        <textarea id="programa" name="programa" rows="4"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="duracao">Duração *</label>
                            <input type="text" id="duracao" name="duracao" placeholder="Ex: 3 meses" required>
                        </div>
                        <div class="form-group">
                            <label for="preco">Preço (€) *</label>
                            <input type="number" step="0.01" id="preco" name="preco" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="area">Área *</label>
                            <input type="text" id="area" name="area" placeholder="Ex: Tecnologia" required>
                        </div>
                        <div class="form-group">
                            <label for="modalidade">Modalidade *</label>
                            <select id="modalidade" name="modalidade" required>
                                <option value="">Selecione</option>
                                <option value="presencial">Presencial</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="imagem_url">URL da Imagem</label>
                        <input type="url" id="imagem_url" name="imagem_url" placeholder="https://...">
                    </div>
                    <div class="form-group checkbox">
                        <input type="checkbox" id="ativo" name="ativo" checked>
                        <label for="ativo">Curso ativo</label>
                    </div>
                `;

            case 'formador':
                return `
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">Nome Completo *</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="especialidade">Especialidade</label>
                            <input type="text" id="especialidade" name="especialidade" placeholder="Ex: Programação Web">
                        </div>
                        <div class="form-group">
                            <label for="foto_url">URL da Foto</label>
                            <input type="url" id="foto_url" name="foto_url" placeholder="https://...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bio">Biografia</label>
                        <textarea id="bio" name="bio" rows="3" placeholder="Breve descrição do formador..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Contactos</label>
                        <div class="contact-group" id="contact-group">
                            <div class="contact-item">
                                <input type="tel" name="contacto" placeholder="Número de telefone">
                                <button type="button" class="btn-add-contact" onclick="AdminUIComponents.addContactField()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

            case 'horario':
                const cursoOptions = cursos.map(curso => 
                    `<option value="${curso.id}">${AdminUtils.sanitizeHTML(curso.nome)}</option>`
                ).join('');
                
                return `
                    <div class="form-group">
                        <label for="curso_id">Curso *</label>
                        <select id="curso_id" name="curso_id" required>
                            <option value="">Selecione um curso</option>
                            ${cursoOptions}
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dia_semana">Dia da Semana *</label>
                            <select id="dia_semana" name="dia_semana" required>
                                <option value="">Selecione</option>
                                <option value="Segunda">Segunda-feira</option>
                                <option value="Terça">Terça-feira</option>
                                <option value="Quarta">Quarta-feira</option>
                                <option value="Quinta">Quinta-feira</option>
                                <option value="Sexta">Sexta-feira</option>
                                <option value="Sábado">Sábado</option>
                                <option value="Domingo">Domingo</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="periodo">Período *</label>
                            <select id="periodo" name="periodo" required>
                                <option value="">Selecione</option>
                                <option value="manhã">Manhã</option>
                                <option value="tarde">Tarde</option>
                                <option value="noite">Noite</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hora_inicio">Hora de Início</label>
                            <input type="time" id="hora_inicio" name="hora_inicio">
                        </div>
                        <div class="form-group">
                            <label for="hora_fim">Hora de Fim</label>
                            <input type="time" id="hora_fim" name="hora_fim">
                        </div>
                    </div>
                `;

            default:
                return '<p>Tipo de formulário não reconhecido</p>';
        }
    }

    static setupContactFields() {
        // Contact fields are already setup in the HTML
        // This function can be extended for additional functionality
    }

    static addContactField() {
        const contactGroup = document.getElementById('contact-group');
        if (!contactGroup) return;

        const contactItem = document.createElement('div');
        contactItem.className = 'contact-item';
        contactItem.innerHTML = `
            <input type="tel" name="contacto" placeholder="Número de telefone">
            <button type="button" class="btn-remove-contact" onclick="AdminUIComponents.removeContactField(this)">
                <i class="fas fa-minus"></i>
            </button>
        `;

        contactGroup.appendChild(contactItem);
    }

    static removeContactField(button) {
        const contactItem = button.closest('.contact-item');
        if (contactItem) {
            contactItem.remove();
        }
    }

    static populateFormData(type, id) {
        const dataArray = adminState.getState(type === 'pre-inscricao' ? 'preInscricoes' : `${type}s`);
        const item = dataArray.find(item => item.id === id);
        
        if (!item) return;

        // Populate basic fields
        Object.keys(item).forEach(key => {
            const field = document.getElementById(key);
            if (field) {
                if (field.type === 'checkbox') {
                    field.checked = !!item[key];
                } else if (key === 'contactos') {
                    // Handle contacts specially
                    this.populateContactFields(item[key]);
                } else {
                    field.value = item[key] || '';
                }
            }
        });
    }

    static populateContactFields(contacts) {
        const contactGroup = document.getElementById('contact-group');
        if (!contactGroup) return;

        const contactsArray = AdminUtils.parseContacts(contacts);
        
        // Clear existing fields
        contactGroup.innerHTML = '';

        // Add fields for each contact or ensure at least one field
        if (contactsArray.length === 0) {
            // Add empty field if no contacts
            const contactItem = document.createElement('div');
            contactItem.className = 'contact-item';
            contactItem.innerHTML = `
                <input type="tel" name="contacto" placeholder="Número de telefone">
                <button type="button" class="btn-add-contact" onclick="AdminUIComponents.addContactField()">
                    <i class="fas fa-plus"></i>
                </button>
            `;
            contactGroup.appendChild(contactItem);
        } else {
            // Add fields for each contact
            contactsArray.forEach((contact, index) => {
                const contactItem = document.createElement('div');
                contactItem.className = 'contact-item';
                contactItem.innerHTML = `
                    <input type="tel" name="contacto" value="${AdminUtils.sanitizeHTML(contact)}" placeholder="Número de telefone">
                    ${index === 0 ? `
                        <button type="button" class="btn-add-contact" onclick="AdminUIComponents.addContactField()">
                            <i class="fas fa-plus"></i>
                        </button>
                    ` : `
                        <button type="button" class="btn-remove-contact" onclick="AdminUIComponents.removeContactField(this)">
                            <i class="fas fa-minus"></i>
                        </button>
                    `}
                `;
                contactGroup.appendChild(contactItem);
            });
        }
    }
}

// Admin Navigation Controller
class AdminNavigationController {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupSidebar();
    }

    setupEventListeners() {
        // Navigation items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const section = item.getAttribute('data-section');
                this.navigateToSection(section);
            });
        });

        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => this.toggleSidebar());
        }

        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', () => this.toggleMobileSidebar());
        }
    }

    setupSidebar() {
        // Initial sidebar state
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('show');
        }
    }

    navigateToSection(sectionId) {
        // Hide all sections
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('active');
        });

        // Show target section
        const targetSection = document.getElementById(`${sectionId}-section`);
        if (targetSection) {
            targetSection.classList.add('active');
        }

        // Update navigation
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });

        const activeNavItem = document.querySelector(`[data-section="${sectionId}"]`);
        if (activeNavItem) {
            activeNavItem.classList.add('active');
        }

        // Update page title and subtitle
        this.updatePageTitle(sectionId);

        // Update state
        adminState.setUIState('currentSection', sectionId);

        // Load section data if needed
        this.loadSectionData(sectionId);
    }

    updatePageTitle(sectionId) {
        const titleElement = document.getElementById('page-title');
        const subtitleElement = document.getElementById('page-subtitle');

        const titles = {
            dashboard: { title: 'Dashboard', subtitle: 'Visão geral do sistema' },
            centros: { title: 'Gestão de Centros', subtitle: 'Gerencie os centros de formação' },
            cursos: { title: 'Gestão de Cursos', subtitle: 'Gerencie os cursos disponíveis' },
            formadores: { title: 'Gestão de Formadores', subtitle: 'Gerencie os formadores do sistema' },
            horarios: { title: 'Gestão de Horários', subtitle: 'Gerencie os horários dos cursos' },
            'pre-inscricoes': { title: 'Gestão de Pré-inscrições', subtitle: 'Gerencie as pré-inscrições dos estudantes' }
        };

        const pageInfo = titles[sectionId] || { title: 'Painel Admin', subtitle: 'Sistema de gestão' };
        
        if (titleElement) titleElement.textContent = pageInfo.title;
        if (subtitleElement) subtitleElement.textContent = pageInfo.subtitle;
    }

    loadSectionData(sectionId) {
        switch (sectionId) {
            case 'dashboard':
                adminDataController.loadDashboard();
                break;
            case 'centros':
                adminDataController.renderCentrosTable();
                break;
            case 'cursos':
                adminDataController.renderCursosTable();
                break;
            case 'formadores':
                adminDataController.renderFormadoresTable();
                break;
            case 'horarios':
                adminDataController.renderHorariosTable();
                break;
            case 'pre-inscricoes':
                adminDataController.renderPreInscricoesTable();
                break;
        }
    }

    toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        adminState.setUIState('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }

    toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('show');
    }
}

// Admin Data Controller
class AdminDataController {
    constructor() {
        this.setupStateSubscriptions();
        this.loadAllData();
        this.setupFormSubmission();
        this.setupSearch();
    }

    setupStateSubscriptions() {
        adminState.subscribe('centros', () => this.updateStats());
        adminState.subscribe('cursos', () => this.updateStats());
        adminState.subscribe('formadores', () => this.updateStats());
        adminState.subscribe('preInscricoes', () => this.updateStats());
    }

    setupFormSubmission() {
        const modalForm = document.getElementById('modal-form');
        if (modalForm) {
            modalForm.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }
    }

    setupSearch() {
        // Setup search for each section
        ['centros', 'cursos', 'formadores', 'horarios', 'pre-inscricoes'].forEach(section => {
            const searchInput = document.getElementById(`${section}-search`);
            if (searchInput) {
                searchInput.addEventListener('input', AdminUtils.debounce((e) => {
                    adminState.setUIState('searchTerms', {
                        ...adminState.getUIState('searchTerms'),
                        [section]: e.target.value
                    });
                    this.filterAndRenderTable(section);
                }, ADMIN_CONFIG.DEBOUNCE_DELAY));
            }
        });
    }

    async loadAllData() {
        AdminUIComponents.showLoading(true);
        
        try {
            const [centros, cursos, formadores, horarios, preInscricoes] = await Promise.allSettled([
                adminApi.getAll('centros'),
                adminApi.getAll('cursos'),
                adminApi.getAll('formadores'),
                adminApi.getAll('horarios'),
                adminApi.getAll('pre-inscricoes')
            ]);

            // Handle results
            if (centros.status === 'fulfilled') adminState.setState('centros', centros.value);
            if (cursos.status === 'fulfilled') adminState.setState('cursos', cursos.value);
            if (formadores.status === 'fulfilled') adminState.setState('formadores', formadores.value);
            if (horarios.status === 'fulfilled') adminState.setState('horarios', horarios.value);
            if (preInscricoes.status === 'fulfilled') adminState.setState('preInscricoes', preInscricoes.value);

            // Check for failures
            const failures = [centros, cursos, formadores, horarios, preInscricoes]
                .filter(result => result.status === 'rejected');

            if (failures.length > 0) {
                console.warn('Some admin data failed to load:', failures);
                AdminUIComponents.showNotification(
                    'Alguns dados podem estar indisponíveis.',
                    'warning'
                );
            }

            console.log('Admin data loaded:', {
                centros: adminState.getState('centros').length,
                cursos: adminState.getState('cursos').length,
                formadores: adminState.getState('formadores').length,
                horarios: adminState.getState('horarios').length,
                preInscricoes: adminState.getState('preInscricoes').length
            });

        } catch (error) {
            console.error('Error loading admin data:', error);
            AdminUIComponents.showNotification('Erro ao carregar dados administrativos.', 'error');
        } finally {
            AdminUIComponents.showLoading(false);
        }
    }

    loadDashboard() {
        this.updateStats();
        this.renderCoursesByCenter();
        this.renderRecentRegistrations();
    }

    updateStats() {
        const centros = adminState.getState('centros');
        const cursos = adminState.getState('cursos');
        const formadores = adminState.getState('formadores');
        const preInscricoes = adminState.getState('preInscricoes');

        // Animate counters
        const totalCentros = document.getElementById('total-centros');
        const totalCursos = document.getElementById('total-cursos');
        const totalFormadores = document.getElementById('total-formadores');
        const totalPreInscricoes = document.getElementById('total-pre-inscricoes');

        if (totalCentros) AdminUtils.animateCounter(totalCentros, 0, centros.length);
        if (totalCursos) AdminUtils.animateCounter(totalCursos, 0, cursos.length);
        if (totalFormadores) AdminUtils.animateCounter(totalFormadores, 0, formadores.length);
        if (totalPreInscricoes) AdminUtils.animateCounter(totalPreInscricoes, 0, preInscricoes.length);

        // Update header stats
        const headerTotalCourses = document.getElementById('header-total-courses');
        const headerTotalRegistrations = document.getElementById('header-total-registrations');
        
        if (headerTotalCourses) headerTotalCourses.textContent = cursos.length;
        if (headerTotalRegistrations) headerTotalRegistrations.textContent = preInscricoes.length;
    }

    renderCoursesByCenter() {
        const container = document.getElementById('courses-by-center-chart');
        if (!container) return;

        const centros = adminState.getState('centros');
        const cursos = adminState.getState('cursos');

        if (centros.length === 0) {
            container.innerHTML = '<div class="chart-placeholder"><i class="fas fa-chart-bar"></i><p>Nenhum dado disponível</p></div>';
            return;
        }

        const chartData = centros.map(centro => ({
            name: centro.nome,
            count: cursos.filter(curso => curso.centro_id === centro.id).length
        }));

        const maxCount = Math.max(...chartData.map(item => item.count), 1);

        container.innerHTML = chartData.map(item => `
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--gray-200);">
                <span style="font-weight: 500; color: var(--gray-700);">${AdminUtils.sanitizeHTML(item.name)}</span>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 120px; height: 8px; background: var(--gray-200); border-radius: 4px; overflow: hidden;">
                        <div style="width: ${(item.count / maxCount) * 100}%; height: 100%; background: var(--primary-color); transition: width 0.3s ease;"></div>
                    </div>
                    <span style="font-weight: 600; color: var(--primary-color); min-width: 20px; text-align: right;">${item.count}</span>
                </div>
            </div>
        `).join('');
    }

    renderRecentRegistrations() {
        const container = document.getElementById('recent-registrations');
        if (!container) return;

        const preInscricoes = adminState.getState('preInscricoes');
        const cursos = adminState.getState('cursos');

        const recent = preInscricoes
            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
            .slice(0, 5);

        if (recent.length === 0) {
            container.innerHTML = '<div class="chart-placeholder"><i class="fas fa-users"></i><p>Nenhuma pré-inscrição recente</p></div>';
            return;
        }

        container.innerHTML = recent.map(inscricao => {
            const curso = cursos.find(c => c.id === inscricao.curso_id);
            return `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--gray-200);">
                    <div>
                        <div style="font-weight: 500; color: var(--gray-800); margin-bottom: 4px;">${AdminUtils.sanitizeHTML(inscricao.nome_completo || 'Nome não informado')}</div>
                        <div style="font-size: 0.875rem; color: var(--gray-600);">${curso ? AdminUtils.sanitizeHTML(curso.nome) : 'Curso não encontrado'}</div>
                    </div>
                    <span class="status-badge status-${inscricao.status}">${inscricao.status}</span>
                </div>
            `;
        }).join('');
    }

    // Continue with more methods...
    renderCentrosTable() {
        const tbody = document.querySelector('#centros-table tbody');
        if (!tbody) return;

        const centros = adminState.getState('centros');
        const cursos = adminState.getState('cursos');

        if (centros.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--gray-500);">Nenhum centro encontrado</td></tr>';
            return;
        }

        tbody.innerHTML = centros.map(centro => {
            const contactDisplay = AdminUtils.formatContactsDisplay(centro.contactos);
            const cursoCount = cursos.filter(c => c.centro_id === centro.id).length;

            return `
                <tr>
                    <td><strong>${AdminUtils.sanitizeHTML(centro.nome)}</strong></td>
                    <td>${AdminUtils.sanitizeHTML(centro.localizacao)}</td>
                    <td>${centro.email ? AdminUtils.sanitizeHTML(centro.email) : '-'}</td>
                    <td>${contactDisplay}</td>
                    <td><span class="status-badge status-confirmado">${cursoCount} cursos</span></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn-action btn-edit" onclick="editItem('centro', ${centro.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-delete" onclick="deleteItem('centro', ${centro.id})" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        // Update table footer
        this.updateTableFooter('centros', centros.length, centros.length);
    }

    // Similar methods for other tables...
    renderCursosTable() {
        const tbody = document.querySelector('#cursos-table tbody');
        if (!tbody) return;

        const cursos = adminState.getState('cursos');
        const centros = adminState.getState('centros');

        if (cursos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px; color: var(--gray-500);">Nenhum curso encontrado</td></tr>';
            return;
        }

        tbody.innerHTML = cursos.map(curso => {
            const centro = centros.find(c => c.id === curso.centro_id);

            return `
                <tr>
                    <td><strong>${AdminUtils.sanitizeHTML(curso.nome)}</strong></td>
                    <td>${centro ? AdminUtils.sanitizeHTML(centro.nome) : 'Centro não encontrado'}</td>
                    <td>${AdminUtils.sanitizeHTML(curso.area)}</td>
                    <td>${AdminUtils.sanitizeHTML(curso.duracao)}</td>
                    <td>${AdminUtils.formatCurrency(curso.preco)}</td>
                    <td><span class="status-badge status-${curso.modalidade}">${curso.modalidade}</span></td>
                    <td><span class="status-badge status-${curso.ativo ? 'ativo' : 'inativo'}">${curso.ativo ? 'Ativo' : 'Inativo'}</span></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn-action btn-edit" onclick="editItem('curso', ${curso.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-delete" onclick="deleteItem('curso', ${curso.id})" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        this.updateTableFooter('cursos', cursos.length, cursos.length);
    }

    renderFormadoresTable() {
        const tbody = document.querySelector('#formadores-table tbody');
        if (!tbody) return;

        const formadores = adminState.getState('formadores');

        if (formadores.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--gray-500);">Nenhum formador encontrado</td></tr>';
            return;
        }

        tbody.innerHTML = formadores.map(formador => {
            const contactDisplay = AdminUtils.formatContactsDisplay(formador.contactos);

            return `
                <tr>
                    <td><strong>${AdminUtils.sanitizeHTML(formador.nome)}</strong></td>
                    <td>${formador.email ? AdminUtils.sanitizeHTML(formador.email) : '-'}</td>
                    <td>${formador.especialidade ? AdminUtils.sanitizeHTML(formador.especialidade) : '-'}</td>
                    <td>${contactDisplay}</td>
                    <td>-</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn-action btn-edit" onclick="editItem('formador', ${formador.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-delete" onclick="deleteItem('formador', ${formador.id})" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        this.updateTableFooter('formadores', formadores.length, formadores.length);
    }

    renderHorariosTable() {
        const tbody = document.querySelector('#horarios-table tbody');
        if (!tbody) return;

        const horarios = adminState.getState('horarios');
        const cursos = adminState.getState('cursos');

        if (horarios.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--gray-500);">Nenhum horário encontrado</td></tr>';
            return;
        }

        tbody.innerHTML = horarios.map(horario => {
            const curso = cursos.find(c => c.id === horario.curso_id);

            return `
                <tr>
                    <td><strong>${curso ? AdminUtils.sanitizeHTML(curso.nome) : 'Curso não encontrado'}</strong></td>
                    <td>${AdminUtils.sanitizeHTML(horario.dia_semana)}</td>
                    <td>${AdminUtils.sanitizeHTML(horario.periodo)}</td>
                    <td>${horario.hora_inicio || '-'}</td>
                    <td>${horario.hora_fim || '-'}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn-action btn-edit" onclick="editItem('horario', ${horario.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-delete" onclick="deleteItem('horario', ${horario.id})" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        this.updateTableFooter('horarios', horarios.length, horarios.length);
    }

    renderPreInscricoesTable() {
        const tbody = document.querySelector('#pre-inscricoes-table tbody');
        if (!tbody) return;

        const preInscricoes = adminState.getState('preInscricoes');
        const cursos = adminState.getState('cursos');
        const centros = adminState.getState('centros');

        if (preInscricoes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px; color: var(--gray-500);">Nenhuma pré-inscrição encontrada</td></tr>';
            return;
        }

        tbody.innerHTML = preInscricoes.map(inscricao => {
            const curso = cursos.find(c => c.id === inscricao.curso_id);
            const centro = centros.find(c => c.id === inscricao.centro_id);

            return `
                <tr>
                    <td><strong>${AdminUtils.sanitizeHTML(inscricao.nome_completo)}</strong></td>
                    <td>${inscricao.email ? AdminUtils.sanitizeHTML(inscricao.email) : '-'}</td>
                    <td>${curso ? AdminUtils.sanitizeHTML(curso.nome) : 'Curso não encontrado'}</td>
                    <td>${centro ? AdminUtils.sanitizeHTML(centro.nome) : 'Centro não encontrado'}</td>
                    <td><span class="status-badge status-${inscricao.status}">${inscricao.status}</span></td>
                    <td>${AdminUtils.formatDate(inscricao.created_at)}</td>
                    <td>
                        <div class="btn-group">
                            ${inscricao.status === 'pendente' ? `
                                <button class="btn-action btn-view" onclick="updatePreInscricaoStatus(${inscricao.id}, 'confirmado')" title="Confirmar">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn-action btn-delete" onclick="updatePreInscricaoStatus(${inscricao.id}, 'cancelado')" title="Cancelar">
                                    <i class="fas fa-times"></i>
                                </button>
                            ` : ''}
                            <button class="btn-action btn-edit" onclick="editItem('pre-inscricao', ${inscricao.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        this.updateTableFooter('pre-inscricoes', preInscricoes.length, preInscricoes.length);
    }

    updateTableFooter(section, showing, total) {
        const showingElement = document.getElementById(`${section}-showing`);
        const totalElement = document.getElementById(`${section}-total`);

        if (showingElement) showingElement.textContent = showing;
        if (totalElement) totalElement.textContent = total;
    }

    filterAndRenderTable(section) {
        // This method can be extended to implement filtering logic
        switch (section) {
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
        }
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const type = adminState.getUIState('currentModal');
        const editId = adminState.getUIState('currentEditId');
        
        if (!type) return;

        try {
            AdminUIComponents.showLoading(true);
            
            const data = this.collectFormData(formData, type);
            
            if (editId) {
                await adminApi.update(this.getResourceName(type), editId, data);
                AdminUIComponents.showNotification('Item atualizado com sucesso!', 'success');
            } else {
                await adminApi.create(this.getResourceName(type), data);
                AdminUIComponents.showNotification('Item criado com sucesso!', 'success');
            }
            
            AdminUIComponents.closeModal();
            await this.loadAllData();
            
        } catch (error) {
            console.error('Error submitting form:', error);
            AdminUIComponents.showNotification('Erro ao salvar. Tente novamente.', 'error');
        } finally {
            AdminUIComponents.showLoading(false);
        }
    }

    collectFormData(formData, type) {
        const data = {};
        
        // Collect basic form data
        for (let [key, value] of formData.entries()) {
            if (key === 'contacto') {
                // Handle contacts specially
                continue;
            } else if (key === 'ativo') {
                data[key] = true;
            } else {
                data[key] = value || null;
            }
        }
        
        // Handle ativo checkbox for courses
        if (type === 'curso' && !formData.has('ativo')) {
            data.ativo = false;
        }
        
        // Handle contacts
        if (['centro', 'formador'].includes(type)) {
            const contactInputs = document.querySelectorAll('input[name="contacto"]');
            const contacts = Array.from(contactInputs)
                .map(input => input.value.trim())
                .filter(value => value);
            // Enviar como array diretamente, não como JSON string
            data.contactos = contacts;
        }
        
        // Convert numeric fields
        if (type === 'curso') {
            data.preco = parseFloat(data.preco);
            data.centro_id = parseInt(data.centro_id);
        }
        
        if (type === 'horario') {
            data.curso_id = parseInt(data.curso_id);
        }
        
        return data;
    }

    getResourceName(type) {
        const resourceMap = {
            'centro': 'centros',
            'curso': 'cursos',
            'formador': 'formadores',
            'horario': 'horarios',
            'pre-inscricao': 'pre-inscricoes'
        };
        return resourceMap[type] || type;
    }
}

// Global functions for admin
window.openCreateModal = (type) => {
    const titles = {
        'centro': 'Novo Centro',
        'curso': 'Novo Curso',
        'formador': 'Novo Formador',
        'horario': 'Novo Horário'
    };
    AdminUIComponents.openModal(titles[type], type);
};

window.editItem = (type, id) => {
    const titles = {
        'centro': 'Editar Centro',
        'curso': 'Editar Curso',
        'formador': 'Editar Formador',
        'horario': 'Editar Horário',
        'pre-inscricao': 'Editar Pré-inscrição'
    };
    AdminUIComponents.openModal(titles[type], type, id);
};

window.deleteItem = async (type, id) => {
    if (!confirm('Tem certeza que deseja excluir este item?')) {
        return;
    }
    
    try {
        AdminUIComponents.showLoading(true);
        await adminApi.delete(adminDataController.getResourceName(type), id);
        AdminUIComponents.showNotification('Item excluído com sucesso!', 'success');
        await adminDataController.loadAllData();
    } catch (error) {
        console.error('Error deleting item:', error);
        AdminUIComponents.showNotification('Erro ao excluir item.', 'error');
    } finally {
        AdminUIComponents.showLoading(false);
    }
};

window.updatePreInscricaoStatus = async (id, status) => {
    try {
        AdminUIComponents.showLoading(true);
        await adminApi.update('pre-inscricoes', id, { status });
        AdminUIComponents.showNotification('Status atualizado com sucesso!', 'success');
        await adminDataController.loadAllData();
    } catch (error) {
        console.error('Error updating status:', error);
        AdminUIComponents.showNotification('Erro ao atualizar status.', 'error');
    } finally {
        AdminUIComponents.showLoading(false);
    }
};

window.refreshChart = (chartType) => {
    switch (chartType) {
        case 'courses-by-center':
            adminDataController.renderCoursesByCenter();
            break;
        case 'recent-registrations':
            adminDataController.renderRecentRegistrations();
            break;
    }
};

window.reloadAllData = async () => {
    AdminUIComponents.showNotification('Recarregando dados...', 'info');
    await adminDataController.loadAllData();
};

window.closeModal = () => {
    AdminUIComponents.closeModal();
};

window.closeNotification = () => {
    AdminUIComponents.closeNotification();
};

// Admin Application
class AdminApp {
    constructor() {
        this.controllers = {};
        this.init();
    }

    async init() {
        try {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.start());
            } else {
                this.start();
            }
        } catch (error) {
            console.error('Error initializing admin app:', error);
            AdminUIComponents.showNotification('Erro ao inicializar painel administrativo', 'error');
        }
    }

    start() {
        console.log('🚀 FormAcademy Admin - Inicializando painel...');

        // Initialize AOS
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 600,
                easing: 'ease-out',
                once: true,
                offset: 50
            });
        }

        // Initialize controllers
        this.controllers.navigation = new AdminNavigationController();
        this.controllers.data = new AdminDataController();

        // Make data controller globally available
        window.adminDataController = this.controllers.data;

        // Setup global error handling
        this.setupErrorHandling();

        console.log('✅ FormAcademy Admin - Painel inicializado com sucesso!');
    }

    setupErrorHandling() {
        window.addEventListener('error', (e) => {
            console.error('Admin unhandled error:', e.error);
            AdminUIComponents.showNotification('Ocorreu um erro inesperado no painel', 'error');
        });

        window.addEventListener('unhandledrejection', (e) => {
            console.error('Admin unhandled promise rejection:', e.reason);
            AdminUIComponents.showNotification('Erro de comunicação com o servidor', 'error');
        });
    }
}

// Start the admin application
const adminApp = new AdminApp();
