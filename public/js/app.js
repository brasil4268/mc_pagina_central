// Configuração global da aplicação
const APP_CONFIG = {
    API_BASE: window.location.origin + '/api',
    ANIMATION_DURATION: 300,
    DEBOUNCE_DELAY: 500
};

// Classe principal da aplicação
class App {
    constructor() {
        this.currentUser = this.getStoredUser();
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadTheme();
        this.initComponents();
    }

    setupEventListeners() {
        // Listener para mudança de tema
        document.addEventListener('DOMContentLoaded', () => {
            this.initThemeToggle();
        });

        // Listeners para modais
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                this.closeModal(e.target);
            }
        });

        // Listener para tecla ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });

        // Listener para formulários
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('ajax-form')) {
                e.preventDefault();
                this.handleFormSubmit(e.target);
            }
        });
    }

    initComponents() {
        // Inicializar tooltips
        this.initTooltips();
        
        // Inicializar animações
        this.initAnimations();
        
        // Verificar autenticação
        this.checkAuth();
    }

    // Gestão de temas
    loadTheme() {
        const savedTheme = localStorage.getItem('app-theme') || 'light';
        this.setTheme(savedTheme);
    }

    setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('app-theme', theme);
        
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.innerHTML = theme === 'dark' ? 
                '<i class="fas fa-sun"></i>' : 
                '<i class="fas fa-moon"></i>';
        }
    }

    toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
    }

    initThemeToggle() {
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => this.toggleTheme());
        }
    }

    // Gestão de modais
    openModal(modalId, data = null) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Preparar dados do modal se fornecidos
        if (data && typeof data === 'object') {
            this.populateModalData(modal, data);
        }

        modal.classList.add('show');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Foco no primeiro input
        const firstInput = modal.querySelector('input, select, textarea');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }

        // Animação de entrada
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.style.animation = 'slideIn 0.3s ease';
        }
    }

    closeModal(modal) {
        if (typeof modal === 'string') {
            modal = document.getElementById(modal);
        }
        
        if (!modal) return;

        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.style.animation = 'slideOut 0.3s ease';
        }

        setTimeout(() => {
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.style.overflow = '';
            
            // Limpar formulário se existir
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
                this.clearFormErrors(form);
            }
        }, APP_CONFIG.ANIMATION_DURATION);
    }

    closeAllModals() {
        document.querySelectorAll('.modal.show').forEach(modal => {
            this.closeModal(modal);
        });
    }

    populateModalData(modal, data) {
        Object.keys(data).forEach(key => {
            const input = modal.querySelector(`[name="${key}"], #${key}`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = data[key];
                } else {
                    input.value = data[key] || '';
                }
            }
        });
    }

    // Gestão de alertas
    showAlert(type, message, duration = 5000) {
        const alertId = 'alert-' + Date.now();
        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease;">
                <div class="flex-between">
                    <div class="flex gap-1">
                        <i class="fas fa-${this.getAlertIcon(type)}"></i>
                        <span>${message}</span>
                    </div>
                    <button type="button" class="btn-sm" onclick="app.dismissAlert('${alertId}')" style="background: none; border: none; color: inherit; cursor: pointer;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', alertHtml);

        if (duration > 0) {
            setTimeout(() => this.dismissAlert(alertId), duration);
        }

        return alertId;
    }

    dismissAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => alert.remove(), APP_CONFIG.ANIMATION_DURATION);
        }
    }

    getAlertIcon(type) {
        const icons = {
            success: 'check-circle',
            danger: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    // Gestão de formulários
    handleFormSubmit(form) {
        const submitBtn = form.querySelector('[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Mostrar loading
        this.setButtonLoading(submitBtn, true);
        
        // Limpar erros anteriores
        this.clearFormErrors(form);

        // Preparar dados
        const formData = new FormData(form);
        const method = form.getAttribute('data-method') || 'POST';
        const url = form.getAttribute('action') || form.getAttribute('data-url');

        // Fazer requisição
        this.makeRequest(url, method, formData)
            .then(response => {
                this.showAlert('success', 'Operação realizada com sucesso!');
                
                // Fechar modal se existir
                const modal = form.closest('.modal');
                if (modal) {
                    this.closeModal(modal);
                }
                
                // Atualizar dados se necessário
                if (typeof window.loadData === 'function') {
                    window.loadData();
                }
            })
            .catch(error => {
                if (error.errors) {
                    this.showFormErrors(form, error.errors);
                } else {
                    this.showAlert('danger', error.message || 'Erro ao processar solicitação');
                }
            })
            .finally(() => {
                this.setButtonLoading(submitBtn, false, originalText);
            });
    }

    setButtonLoading(button, loading, originalText = null) {
        if (loading) {
            button.disabled = true;
            button.innerHTML = '<span class="loading"></span> Processando...';
        } else {
            button.disabled = false;
            button.innerHTML = originalText || button.getAttribute('data-original-text') || 'Salvar';
        }
    }

    clearFormErrors(form) {
        form.querySelectorAll('.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
        });
        form.querySelectorAll('.invalid-feedback').forEach(feedback => {
            feedback.style.display = 'none';
        });
    }

    showFormErrors(form, errors) {
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = errors[field][0];
                    feedback.style.display = 'block';
                }
            }
        });
    }

    // Requisições HTTP
    async makeRequest(url, method = 'GET', data = null) {
        const options = {
            method: method.toUpperCase(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        };

        // Adicionar token CSRF se disponível
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            options.headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }

        // Adicionar dados se necessário
        if (data) {
            if (data instanceof FormData) {
                options.body = data;
            } else {
                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(data);
            }
        }

        try {
            const response = await fetch(url, options);
            
            // Verificar se a resposta é JSON
            const contentType = response.headers.get('content-type');
            let result;
            
            if (contentType && contentType.includes('application/json')) {
                result = await response.json();
            } else {
                result = { message: await response.text() };
            }

            if (!response.ok) {
                // Estrutura de erro padronizada
                throw {
                    status: response.status,
                    message: result.message || result.mensagem || 'Erro na requisição',
                    errors: result.errors || null
                };
            }

            // Retornar dados em formato consistente
            if (result.dados) {
                return result.dados; // Laravel retorna "dados"
            } else if (result.data) {
                return result.data; // Outros formatos
            } else {
                return result; // Retorno direto
            }
            
        } catch (error) {
            console.error('Request error:', error);
            
            // Se não for um erro estruturado, criar um
            if (!error.status) {
                throw {
                    status: 500,
                    message: 'Erro de conexão. Verifique se o servidor está rodando.',
                    errors: null
                };
            }
            
            throw error;
        }
    }

    // Utilitários
    debounce(func, delay = APP_CONFIG.DEBOUNCE_DELAY) {
        let timeoutId;
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    formatCurrency(value) {
        return new Intl.NumberFormat('pt-AO', {
            style: 'currency',
            currency: 'AOA'
        }).format(value);
    }

    formatDate(date) {
        return new Intl.DateTimeFormat('pt-PT', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(date));
    }

    validatePhone(phone) {
        return /^9\d{8}$/.test(phone);
    }

    validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Gestão de autenticação
    getStoredUser() {
        const userData = localStorage.getItem('app-user');
        return userData ? JSON.parse(userData) : null;
    }

    setStoredUser(user) {
        localStorage.setItem('app-user', JSON.stringify(user));
        this.currentUser = user;
    }

    clearStoredUser() {
        localStorage.removeItem('app-user');
        this.currentUser = null;
    }

    checkAuth() {
        const protectedRoutes = ['dashboard.html'];
        const currentFile = window.location.pathname.split('/').pop();
        
        if (protectedRoutes.includes(currentFile)) {
            if (!this.currentUser) {
                window.location.href = 'login.html';
                return false;
            }
        }
        return true;
    }

    login(credentials) {
        // Sistema de login estático com dois tipos de usuário
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                // Admin: acesso total ao sistema
                if (credentials.email === 'admin@admin.com' && credentials.password === 'admin123') {
                    const user = {
                        id: 1,
                        name: 'Administrador',
                        email: 'admin@admin.com',
                        role: 'admin',
                        avatar: null,
                        permissions: ['create', 'read', 'update', 'delete']
                    };
                    this.setStoredUser(user);
                    resolve(user);
                } 
                // Usuário normal: apenas visualização (credenciais inválidas propositalmente)
                else if (credentials.email === 'user@user.com' && credentials.password === 'user123') {
                    const user = {
                        id: 2,
                        name: 'Usuário Normal',
                        email: 'user@user.com',
                        role: 'user',
                        avatar: null,
                        permissions: ['read']
                    };
                    this.setStoredUser(user);
                    resolve(user);
                } else {
                    reject({ message: 'Credenciais inválidas. Use admin@admin.com/admin123 para acesso administrativo.' });
                }
            }, 1000);
        });
    }

    logout() {
        this.clearStoredUser();
        window.location.href = 'login.html';
    }

    // Inicialização de componentes
    initTooltips() {
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target, e.target.getAttribute('data-tooltip'));
            });
            element.addEventListener('mouseleave', () => {
                this.hideTooltip();
            });
        });
    }

    showTooltip(element, text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = text;
        tooltip.style.cssText = `
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            z-index: 10000;
            white-space: nowrap;
            animation: fadeIn 0.2s ease;
        `;

        document.body.appendChild(tooltip);

        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';

        this.currentTooltip = tooltip;
    }

    hideTooltip() {
        if (this.currentTooltip) {
            this.currentTooltip.remove();
            this.currentTooltip = null;
        }
    }

    initAnimations() {
        // Animações de entrada para elementos visíveis
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.card, .table-container, .stat-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            observer.observe(el);
        });
    }

    // Funcionalidades específicas
    initSearchable(inputId, callback) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const debouncedSearch = this.debounce((value) => {
            callback(value);
        }, 300);

        input.addEventListener('input', (e) => {
            debouncedSearch(e.target.value);
        });
    }

    exportData(data, filename, type = 'json') {
        let content, mimeType;

        switch (type) {
            case 'csv':
                content = this.convertToCSV(data);
                mimeType = 'text/csv';
                filename += '.csv';
                break;
            case 'excel':
                content = this.convertToExcel(data);
                mimeType = 'application/vnd.ms-excel';
                filename += '.xls';
                break;
            default:
                content = JSON.stringify(data, null, 2);
                mimeType = 'application/json';
                filename += '.json';
        }

        const blob = new Blob([content], { type: mimeType });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
        window.URL.revokeObjectURL(url);
    }

    convertToCSV(data) {
        if (!Array.isArray(data) || data.length === 0) return '';

        const headers = Object.keys(data[0]);
        const csvContent = [
            headers.join(','),
            ...data.map(row => headers.map(header => `"${row[header] || ''}"`).join(','))
        ].join('\n');

        return csvContent;
    }

    // Performance e otimização
    preloadImages(urls) {
        urls.forEach(url => {
            const img = new Image();
            img.src = url;
        });
    }

    lazy(fn, delay = 100) {
        return setTimeout(fn, delay);
    }
}

// Instância global da aplicação
const app = new App();

// Funções globais para compatibilidade
window.openModal = (id, data) => app.openModal(id, data);
window.closeModal = (id) => app.closeModal(id);
window.showAlert = (type, message) => app.showAlert(type, message);

// Adicionar animações CSS personalizadas
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    @keyframes slideOut {
        from { transform: translateY(0); opacity: 1; }
        to { transform: translateY(-50px); opacity: 0; }
    }
    
    @keyframes fadeInUp {
        from { 
            opacity: 0; 
            transform: translateY(20px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }
`;
document.head.appendChild(style);

// Exportar para uso em outros módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = App;
}
