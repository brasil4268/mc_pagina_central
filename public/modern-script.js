// Modern Script - Centro de Formação
// ====================================

// Configuration
const CONFIG = {
    API_BASE_URL: '/api',
    ANIMATION_DURATION: 300,
    DEBOUNCE_DELAY: 500,
    MAX_RETRIES: 3,
    RETRY_DELAY: 1000
};

// Global State Management
class AppState {
    constructor() {
        this.data = {
            centers: [],
            courses: [],
            trainers: [],
            schedules: [],
            preRegistrations: []
        };
        this.ui = {
            loading: false,
            currentModal: null,
            activeFilters: {},
            currentCourse: null
        };
        this.listeners = new Map();
    }

    // State management methods
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

    // Observer pattern for state changes
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

const appState = new AppState();

// Utility Functions
const Utils = {
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

    // Throttle function
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    // Format currency - usando DataFormatters
    formatCurrency(amount) {
        if (typeof DataFormatters !== 'undefined') {
            return DataFormatters.formatPrice(amount);
        }
        // Fallback
        return new Intl.NumberFormat('pt-AO', {
            style: 'currency',
            currency: 'AOA',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    },

    // Sanitize HTML
    sanitizeHTML(str) {
        const temp = document.createElement('div');
        temp.textContent = str;
        return temp.innerHTML;
    },

    // Generate unique ID
    generateId() {
        return Math.random().toString(36).substr(2, 9);
    },

    // Animate counter
    animateCounter(element, start, end, duration = 2000) {
        const startTime = performance.now();
        const startValue = start;
        const endValue = end;

        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function (ease-out)
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const currentValue = Math.floor(startValue + (endValue - startValue) * easeOut);
            
            element.textContent = currentValue;
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = endValue;
            }
        }
        
        requestAnimationFrame(updateCounter);
    },

    // Parse contacts field - usando DataFormatters
    parseContacts(contacts) {
        if (typeof DataFormatters !== 'undefined') {
            return DataFormatters.formatContacts(contacts);
        }
        // Fallback se DataFormatters não estiver disponível
        if (Array.isArray(contacts)) {
            return { phones: contacts, displayText: contacts[0] || 'Sem contacto' };
        } else if (typeof contacts === 'string') {
            try {
                const parsed = JSON.parse(contacts);
                const phones = Array.isArray(parsed) ? parsed : [parsed];
                return { phones: phones, displayText: phones[0] || 'Sem contacto' };
            } catch {
                return { phones: [contacts], displayText: contacts };
            }
        }
        return { phones: [], displayText: 'Sem contacto' };
    },

    // Truncate text
    truncateText(text, maxLength) {
        if (!text || text.length <= maxLength) return text;
        return text.substring(0, maxLength).trim() + '...';
    }
};

// API Service
class ApiService {
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
            this.retryCount = 0; // Reset retry count on success
            
            // Handle both response formats
            return data.dados || data;
        } catch (error) {
            console.error(`API Error (${endpoint}):`, error);
            
            // Retry logic for network errors
            if (this.retryCount < CONFIG.MAX_RETRIES && this.isRetryableError(error)) {
                this.retryCount++;
                await this.delay(CONFIG.RETRY_DELAY * this.retryCount);
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

    // CRUD Methods
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

const api = new ApiService(CONFIG.API_BASE_URL);

// UI Components
class UIComponents {
    static showLoading(show = true) {
        const loading = document.getElementById('loading');
        if (loading) {
            if (show) {
                loading.classList.add('show');
                appState.setUIState('loading', true);
            } else {
                loading.classList.remove('show');
                appState.setUIState('loading', false);
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

    static openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            appState.setUIState('currentModal', modalId);
        }
    }

    static closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
            appState.setUIState('currentModal', null);
        }
    }

    static createCenterCard(center) {
        // Usar DataFormatters se disponível
        const formattedCenter = (typeof DataFormatters !== 'undefined') ? 
            DataFormatters.formatCentro(center) : center;
        
        const contacts = formattedCenter.contacts || Utils.parseContacts(center.contactos);
        const courseCount = appState.getState('courses').filter(c => c.centro_id === center.id).length;
        
        return `
            <div class="center-card" data-aos="fade-up" data-aos-duration="600" onclick="CenterDetail.show(${center.id})">
                <div class="center-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="center-title">${formattedCenter.nome || Utils.sanitizeHTML(center.nome)}</h3>
                <div class="center-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${formattedCenter.localizacao || Utils.sanitizeHTML(center.localizacao)}</span>
                </div>
                <div class="center-info">
                    ${formattedCenter.email && formattedCenter.email !== 'Email não informado' ? `
                        <div class="center-info-item">
                            <i class="center-info-icon fas fa-envelope"></i>
                            <span>${formattedCenter.email}</span>
                        </div>
                    ` : ''}
                    ${contacts.phones && contacts.phones.length > 0 ? `
                        <div class="center-info-item">
                            <i class="center-info-icon fas fa-phone"></i>
                            <span>${contacts.displayText}</span>
                        </div>
                    ` : ''}
                    <div class="center-info-item">
                        <i class="center-info-icon fas fa-graduation-cap"></i>
                        <span>${courseCount} cursos disponíveis</span>
                    </div>
                </div>
                <div class="center-action">
                    <span>Ver Cursos Disponíveis</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>
        `;
    }

    static createCourseCard(course) {
        // Usar DataFormatters se disponível
        const formattedCourse = (typeof DataFormatters !== 'undefined') ? 
            DataFormatters.formatCurso(course) : course;
        
        const center = appState.getState('centers').find(c => c.id === course.centro_id);
        const schedules = appState.getState('schedules').filter(s => s.curso_id === course.id);
        
        return `
            <div class="course-card" data-aos="fade-up" data-aos-duration="600" onclick="CourseModal.open(${course.id})">
                <div class="course-image">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="course-badge ${formattedCourse.modalidade_formatted?.class || ''}">${formattedCourse.modalidade_formatted?.text || Utils.sanitizeHTML(course.modalidade)}</div>
                <div class="course-content">
                    <h3 class="course-title">${formattedCourse.nome || Utils.sanitizeHTML(course.nome)}</h3>
                    <p class="course-description">${formattedCourse.descricao_short || Utils.truncateText(course.descricao || 'Descrição não disponível', 120)}</p>
                    
                    <div class="course-meta">
                        <div class="course-meta-item">
                            <i class="course-meta-icon fas fa-clock"></i>
                            <span>${formattedCourse.duracao || Utils.sanitizeHTML(course.duracao)}</span>
                        </div>
                        <div class="course-meta-item">
                            <i class="course-meta-icon fas fa-tag"></i>
                            <span>${formattedCourse.area || Utils.sanitizeHTML(course.area)}</span>
                        </div>
                        <div class="course-meta-item">
                            <i class="course-meta-icon fas fa-building"></i>
                            <span>${center ? Utils.sanitizeHTML(center.nome) : 'Centro não definido'}</span>
                        </div>
                        <div class="course-meta-item">
                            <i class="course-meta-icon fas fa-calendar"></i>
                            <span>${schedules.length} horários</span>
                        </div>
                    </div>
                    
                    <div class="course-footer">
                        <div class="course-price">${formattedCourse.preco_formatted || Utils.formatCurrency(course.preco)}</div>
                        <div class="course-action">
                            <span>Ver Detalhes e Inscrever-se</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    static createSkeletonLoader(count = 3) {
        return Array(count).fill(0).map(() => '<div class="skeleton-card"></div>').join('');
    }

    static createNoDataMessage(icon, message) {
        return `
            <div class="no-data">
                <i class="${icon}"></i>
                <p>${message}</p>
            </div>
        `;
    }
}

// Navigation Controller
class NavigationController {
    constructor() {
        this.activeSection = 'home';
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupScrollEffects();
        this.updateActiveSection();
    }

    setupEventListeners() {
        // Navigation links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const target = link.getAttribute('data-target');
                if (target) {
                    this.navigateToSection(target);
                }
            });
        });

        // Mobile menu toggle
        const navToggle = document.getElementById('nav-toggle');
        const navMenu = document.getElementById('nav-menu');
        
        if (navToggle && navMenu) {
            navToggle.addEventListener('click', () => {
                navMenu.classList.toggle('show');
            });

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                    navMenu.classList.remove('show');
                }
            });
        }

        // Scroll spy
        window.addEventListener('scroll', Utils.throttle(() => {
            this.updateActiveSection();
            this.updateNavbarStyle();
        }, 100));
    }

    setupScrollEffects() {
        // Smooth scroll to sections
        window.scrollToSection = (sectionId) => {
            this.navigateToSection(sectionId);
        };
    }

    navigateToSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
            const headerHeight = document.querySelector('.navbar').offsetHeight;
            const sectionTop = section.offsetTop - headerHeight;
            
            window.scrollTo({
                top: sectionTop,
                behavior: 'smooth'
            });
        }
    }

    updateActiveSection() {
        const sections = document.querySelectorAll('.section, .hero');
        const navLinks = document.querySelectorAll('.nav-link');
        const headerHeight = document.querySelector('.navbar').offsetHeight;
        const scrollPosition = window.scrollY + headerHeight + 100;

        let currentSection = 'home';

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionBottom = sectionTop + section.offsetHeight;

            if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                currentSection = section.id;
            }
        });

        if (currentSection !== this.activeSection) {
            this.activeSection = currentSection;
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-target') === currentSection) {
                    link.classList.add('active');
                }
            });
        }
    }

    updateNavbarStyle() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 100) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
}

// Data Controller
class DataController {
    constructor() {
        this.loadingPromises = new Map();
        this.init();
    }

    init() {
        this.setupStateSubscriptions();
        this.loadAllData();
    }

    setupStateSubscriptions() {
        appState.subscribe('centers', () => this.renderCenters());
        appState.subscribe('courses', () => this.renderCourses());
        appState.subscribe('ui.loading', (loading) => {
            if (!loading) {
                this.updateStats();
            }
        });
    }

    async loadAllData() {
        UIComponents.showLoading(true);
        
        try {
            const [centers, courses, trainers, schedules] = await Promise.allSettled([
                this.loadCenters(),
                this.loadCourses(),
                this.loadTrainers(),
                this.loadSchedules()
            ]);

            // Handle any rejected promises
            const failures = [centers, courses, trainers, schedules]
                .filter(result => result.status === 'rejected')
                .map(result => result.reason);

            if (failures.length > 0) {
                console.warn('Some data failed to load:', failures);
                UIComponents.showNotification(
                    'Alguns dados podem estar indisponíveis. Tente recarregar a página.',
                    'warning'
                );
            }

            this.populateFilters();
            
        } catch (error) {
            console.error('Error loading data:', error);
            UIComponents.showNotification('Erro ao carregar dados. Verifique sua conexão.', 'error');
        } finally {
            UIComponents.showLoading(false);
        }
    }

    async loadCenters() {
        try {
            const data = await api.getAll('centros');
            appState.setState('centers', data);
            return data;
        } catch (error) {
            console.error('Error loading centers:', error);
            appState.setState('centers', []);
            throw error;
        }
    }

    async loadCourses() {
        try {
            const data = await api.getAll('cursos');
            appState.setState('courses', data);
            return data;
        } catch (error) {
            console.error('Error loading courses:', error);
            appState.setState('courses', []);
            throw error;
        }
    }

    async loadTrainers() {
        try {
            const data = await api.getAll('formadores');
            appState.setState('trainers', data);
            return data;
        } catch (error) {
            console.error('Error loading trainers:', error);
            appState.setState('trainers', []);
            throw error;
        }
    }

    async loadSchedules() {
        try {
            const data = await api.getAll('horarios');
            appState.setState('schedules', data);
            return data;
        } catch (error) {
            console.error('Error loading schedules:', error);
            appState.setState('schedules', []);
            throw error;
        }
    }

    renderCenters() {
        const grid = document.getElementById('centers-grid');
        if (!grid) return;

        const centers = appState.getState('centers');
        
        if (centers.length === 0) {
            grid.innerHTML = UIComponents.createNoDataMessage(
                'fas fa-building',
                'Nenhum centro disponível no momento'
            );
            return;
        }

        grid.innerHTML = centers.map(center => UIComponents.createCenterCard(center)).join('');
        
        // Reinitialize AOS for new elements
        if (typeof AOS !== 'undefined') {
            AOS.refresh();
        }
    }

    renderCourses(filteredCourses = null) {
        const grid = document.getElementById('courses-grid');
        if (!grid) return;

        const courses = filteredCourses || appState.getState('courses');
        
        if (courses.length === 0) {
            grid.innerHTML = UIComponents.createNoDataMessage(
                'fas fa-graduation-cap',
                'Nenhum curso encontrado com os filtros selecionados'
            );
            return;
        }

        grid.innerHTML = courses.map(course => UIComponents.createCourseCard(course)).join('');
        
        // Reinitialize AOS for new elements
        if (typeof AOS !== 'undefined') {
            AOS.refresh();
        }
    }

    populateFilters() {
        const centers = appState.getState('centers');
        const courses = appState.getState('courses');
        
        // Populate center filter
        const centerFilter = document.getElementById('center-filter');
        if (centerFilter) {
            const options = centers.map(center => 
                `<option value="${center.id}">${Utils.sanitizeHTML(center.nome)}</option>`
            ).join('');
            centerFilter.innerHTML = '<option value="">Todos os Centros</option>' + options;
        }

        // Populate area filter
        const areaFilter = document.getElementById('area-filter');
        if (areaFilter) {
            const areas = [...new Set(courses.map(course => course.area))].filter(Boolean);
            const options = areas.map(area => 
                `<option value="${area}">${Utils.sanitizeHTML(area)}</option>`
            ).join('');
            areaFilter.innerHTML = '<option value="">Todas as Áreas</option>' + options;
        }
    }

    updateStats() {
        const centers = appState.getState('centers');
        const courses = appState.getState('courses');
        const trainers = appState.getState('trainers');

        // Animate counters
        const statCenters = document.getElementById('stat-centers');
        const statCourses = document.getElementById('stat-courses');
        const statTrainers = document.getElementById('stat-trainers');

        if (statCenters) Utils.animateCounter(statCenters, 0, centers.length, 1500);
        if (statCourses) Utils.animateCounter(statCourses, 0, courses.length, 1800);
        if (statTrainers) Utils.animateCounter(statTrainers, 0, trainers.length, 2100);
    }
}

// Filter Controller
class FilterController {
    constructor() {
        this.filters = {
            center: '',
            area: '',
            modalidade: ''
        };
        this.init();
    }

    init() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        const centerFilter = document.getElementById('center-filter');
        const areaFilter = document.getElementById('area-filter');
        const modalidadeFilter = document.getElementById('modalidade-filter');

        const debouncedFilter = Utils.debounce(() => this.applyFilters(), CONFIG.DEBOUNCE_DELAY);

        [centerFilter, areaFilter, modalidadeFilter].forEach(filter => {
            if (filter) {
                filter.addEventListener('change', (e) => {
                    const filterType = e.target.id.replace('-filter', '');
                    this.filters[filterType] = e.target.value;
                    debouncedFilter();
                });
            }
        });

        // Clear filters
        window.clearFilters = () => this.clearFilters();
    }

    applyFilters() {
        const courses = appState.getState('courses').filter(course => course.ativo);
        
        const filteredCourses = courses.filter(course => {
            const centerMatch = !this.filters.center || course.centro_id == this.filters.center;
            const areaMatch = !this.filters.area || course.area === this.filters.area;
            const modalidadeMatch = !this.filters.modalidade || course.modalidade === this.filters.modalidade;
            
            return centerMatch && areaMatch && modalidadeMatch;
        });

        // Update UI state
        appState.setUIState('activeFilters', { ...this.filters });
        
        // Render filtered courses
        dataController.renderCourses(filteredCourses);
    }

    clearFilters() {
        this.filters = { center: '', area: '', modalidade: '' };
        
        // Reset filter UI
        document.getElementById('center-filter').value = '';
        document.getElementById('area-filter').value = '';
        document.getElementById('modalidade-filter').value = '';
        
        // Clear active filters state
        appState.setUIState('activeFilters', {});
        
        // Re-render all courses
        dataController.renderCourses();
    }
}

// Course Modal Controller
class CourseModal {
    static async open(courseId) {
        const course = appState.getState('courses').find(c => c.id === courseId);
        if (!course) return;

        appState.setUIState('currentCourse', course);
        
        // Populate modal content
        this.populateModalContent(course);
        
        // Load additional data
        await this.loadCourseDetails(courseId);
        
        UIComponents.openModal('course-modal');
    }

    static populateModalContent(course) {
        // Usar DataFormatters se disponível
        const formattedCourse = (typeof DataFormatters !== 'undefined') ? 
            DataFormatters.formatCurso(course) : course;
            
        const center = appState.getState('centers').find(c => c.id === course.centro_id);
        
        // Basic course info
        document.getElementById('modal-course-title').textContent = formattedCourse.nome || course.nome;
        document.getElementById('modal-course-duration').textContent = formattedCourse.duracao || course.duracao;
        document.getElementById('modal-course-price').textContent = formattedCourse.preco_formatted || Utils.formatCurrency(course.preco);
        document.getElementById('modal-course-area').textContent = formattedCourse.area || course.area;
        document.getElementById('modal-course-modalidade').textContent = formattedCourse.modalidade_formatted?.text || course.modalidade;
        document.getElementById('modal-course-description').textContent = formattedCourse.descricao || course.descricao || 'Descrição não disponível';
        document.getElementById('modal-course-program').textContent = formattedCourse.programa || course.programa || 'Programa não disponível';
        
        // Setup pre-registration button
        const preRegisterBtn = document.getElementById('pre-register-btn');
        if (preRegisterBtn) {
            preRegisterBtn.onclick = () => PreRegistrationModal.open(course);
        }
    }

    static async loadCourseDetails(courseId) {
        try {
            // Load schedules
            const schedules = appState.getState('schedules').filter(s => s.curso_id === courseId);
            this.renderSchedules(schedules);
            
            // Load course details with trainers
            const courseDetails = await api.getById('cursos', courseId);
            if (courseDetails.formadores) {
                this.renderTrainers(courseDetails.formadores);
            } else {
                this.renderTrainers([]);
            }
        } catch (error) {
            console.error('Error loading course details:', error);
            this.renderSchedules([]);
            this.renderTrainers([]);
        }
    }

    static renderSchedules(schedules) {
        const container = document.getElementById('modal-course-schedules');
        if (!container) return;

        if (schedules.length === 0) {
            container.innerHTML = '<div class="schedule-item">Horários não disponíveis</div>';
            return;
        }

        // Agrupar horários por período (manhã, tarde, noite)
        const schedulesByPeriod = schedules.reduce((acc, schedule) => {
            const period = schedule.periodo?.toLowerCase() || 'outros';
            if (!acc[period]) acc[period] = [];
            acc[period].push(schedule);
            return acc;
        }, {});

        container.innerHTML = Object.entries(schedulesByPeriod).map(([period, periodSchedules]) => `
            <div class="schedule-period">
                <div class="schedule-period-title">${period.charAt(0).toUpperCase() + period.slice(1)}</div>
                ${periodSchedules.map(schedule => `
                    <div class="schedule-item">
                        <div class="schedule-day-time">
                            <strong>${Utils.sanitizeHTML(schedule.dia_semana || 'Não definido')}</strong>
                            ${schedule.hora_inicio && schedule.hora_fim ? `
                                <span class="schedule-time">${schedule.hora_inicio} - ${schedule.hora_fim}</span>
                            ` : ''}
                        </div>
                        ${schedule.horas_por_dia ? `
                            <div class="schedule-hours">
                                <small><i class="fas fa-clock"></i> ${schedule.horas_por_dia}h por dia</small>
                            </div>
                        ` : ''}
                    </div>
                `).join('')}
            </div>
        `).join('');

        // Calcular e mostrar carga horária total
        this.calculateAndDisplayTotalHours(schedules);
    }

    static calculateAndDisplayTotalHours(schedules) {
        const workloadContainer = document.getElementById('modal-course-workload');
        if (!workloadContainer) return;

        const course = appState.getUIState('currentCourse');
        if (!course) return;

        // Calcular horas totais baseado nos horários
        let totalWeeklyHours = 0;
        schedules.forEach(schedule => {
            if (schedule.horas_por_dia) {
                totalWeeklyHours += parseFloat(schedule.horas_por_dia);
            } else if (schedule.hora_inicio && schedule.hora_fim) {
                // Calcular horas se não estiver especificado horas_por_dia
                const start = this.timeToMinutes(schedule.hora_inicio);
                const end = this.timeToMinutes(schedule.hora_fim);
                totalWeeklyHours += (end - start) / 60;
            }
        });

        // Extrair duração em meses da string (ex: "2 meses")
        const durationMatch = course.duracao?.match(/(\d+)\s*(mês|meses|semana|semanas)/i);
        let totalHours = 0;
        
        if (durationMatch) {
            const number = parseInt(durationMatch[1]);
            const unit = durationMatch[2].toLowerCase();
            
            if (unit.includes('mês')) {
                // Assumindo ~4 semanas por mês
                totalHours = totalWeeklyHours * number * 4;
            } else if (unit.includes('semana')) {
                totalHours = totalWeeklyHours * number;
            }
        }

        // Mostrar carga horária
        if (totalHours > 0) {
            workloadContainer.innerHTML = `
                <div class="course-workload">
                    <div class="workload-item">
                        <i class="fas fa-calendar-week"></i>
                        <span>Carga horária semanal: <strong>${totalWeeklyHours}h</strong></span>
                    </div>
                    <div class="workload-item">
                        <i class="fas fa-clock"></i>
                        <span>Carga horária total: <strong>${totalHours}h</strong></span>
                    </div>
                    <div class="workload-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Duração: <strong>${course.duracao}</strong></span>
                    </div>
                </div>
            `;
        } else {
            workloadContainer.innerHTML = `
                <div class="course-workload">
                    <div class="workload-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Duração: <strong>${course.duracao}</strong></span>
                    </div>
                </div>
            `;
        }
    }

    static timeToMinutes(timeStr) {
        if (!timeStr) return 0;
        const [hours, minutes] = timeStr.split(':').map(num => parseInt(num));
        return hours * 60 + (minutes || 0);
    }

    static renderTrainers(trainers) {
        const container = document.getElementById('modal-course-trainers');
        if (!container) return;

        if (trainers.length === 0) {
            container.innerHTML = '<div class="trainer-item">Formadores não definidos</div>';
            return;
        }

        container.innerHTML = trainers.map(trainer => `
            <div class="trainer-item">
                <div class="trainer-avatar">
                    ${trainer.nome.charAt(0).toUpperCase()}
                </div>
                <div class="trainer-info">
                    <div class="trainer-name">${Utils.sanitizeHTML(trainer.nome)}</div>
                    ${trainer.especialidade ? `
                        <div class="trainer-specialty">${Utils.sanitizeHTML(trainer.especialidade)}</div>
                    ` : ''}
                </div>
            </div>
        `).join('');
    }
}

// Center Detail Controller
class CenterDetail {
    static show(centerId) {
        const center = appState.getState('centers').find(c => c.id === centerId);
        if (!center) return;

        // Filtrar cursos deste centro
        const centerCourses = appState.getState('courses').filter(c => c.centro_id === centerId && c.ativo);
        
        // Navegar para a seção de cursos
        navigationController.navigateToSection('courses');
        
        // Aplicar filtro automaticamente
        document.getElementById('center-filter').value = centerId;
        filterController.filters.center = centerId.toString();
        filterController.applyFilters();
        
        // Mostrar notificação com informações do centro
        const message = `Mostrando ${centerCourses.length} cursos disponíveis no centro "${center.nome}" em ${center.localizacao}`;
        UIComponents.showNotification(message, 'info', 4000);
    }
}

// Pre-registration Modal Controller
class PreRegistrationModal {
    static open(course) {
        if (!course) return;

        // Populate course info
        document.getElementById('pr_curso_id').value = course.id;
        document.getElementById('pr_centro_id').value = course.centro_id;

        // Populate schedule options - apenas períodos padrão (manhã, tarde, noite)
        const scheduleSelect = document.getElementById('horario_id');
        
        if (scheduleSelect) {
            // Opções fixas de período - apenas manhã, tarde e noite
            const periodOptions = [
                { value: 'manha', label: 'Manhã' },
                { value: 'tarde', label: 'Tarde' },
                { value: 'noite', label: 'Noite' }
            ];
            
            const options = periodOptions.map(period => 
                `<option value="${period.value}">${period.label}</option>`
            ).join('');
            scheduleSelect.innerHTML = '<option value="">Selecione um período</option>' + options;
        }

        UIComponents.closeModal('course-modal');
        UIComponents.openModal('preregister-modal');
    }

    static async submit(formData) {
        try {
            UIComponents.showLoading(true);

            const data = {
                curso_id: parseInt(formData.get('curso_id')),
                centro_id: parseInt(formData.get('centro_id')),
                horario_id: null, // Não usamos horário específico por enquanto
                nome_completo: formData.get('nome_completo'),
                email: formData.get('email'),
                contactos: [formData.get('telefone')], // Array format como backend espera
                observacoes: formData.get('observacoes') ? 
                    `Período preferido: ${formData.get('horario_id')}. ${formData.get('observacoes')}` : 
                    `Período preferido: ${formData.get('horario_id')}`
            };

            await api.create('pre-inscricoes', data);
            
            UIComponents.showNotification('Pré-inscrição enviada com sucesso! Entraremos em contacto brevemente.', 'success');
            UIComponents.closeModal('preregister-modal');
            
            // Reset form
            document.getElementById('preregister-form').reset();
            
        } catch (error) {
            console.error('Error submitting pre-registration:', error);
            UIComponents.showNotification('Erro ao enviar pré-inscrição. Tente novamente.', 'error');
        } finally {
            UIComponents.showLoading(false);
        }
    }
}

// Form Controller
class FormController {
    constructor() {
        this.init();
    }

    init() {
        this.setupFormValidation();
        this.setupFormSubmissions();
    }

    setupFormValidation() {
        // Add real-time validation for form fields
        document.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('blur', () => this.validateField(field));
            field.addEventListener('input', () => this.clearFieldError(field));
        });
    }

    setupFormSubmissions() {
        // Pre-registration form
        const preRegisterForm = document.getElementById('preregister-form');
        if (preRegisterForm) {
            preRegisterForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                if (this.validateForm(preRegisterForm)) {
                    const formData = new FormData(preRegisterForm);
                    await PreRegistrationModal.submit(formData);
                }
            });
        }

        // Contact form
        const contactForm = document.getElementById('contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                if (this.validateForm(contactForm)) {
                    UIComponents.showNotification('Mensagem enviada com sucesso!', 'success');
                    contactForm.reset();
                }
            });
        }
    }

    validateField(field) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required');
        let isValid = true;
        let errorMessage = '';

        // Required field validation
        if (isRequired && !value) {
            isValid = false;
            errorMessage = 'Este campo é obrigatório';
        }

        // Email validation
        if (field.type === 'email' && value && !this.isValidEmail(value)) {
            isValid = false;
            errorMessage = 'Email inválido';
        }

        // Phone validation
        if (field.type === 'tel' && value && !this.isValidPhone(value)) {
            isValid = false;
            errorMessage = 'Telefone inválido';
        }

        this.displayFieldError(field, isValid ? '' : errorMessage);
        return isValid;
    }

    validateForm(form) {
        const fields = form.querySelectorAll('input, textarea, select');
        let isValid = true;

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isValidPhone(phone) {
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{9,}$/;
        return phoneRegex.test(phone);
    }

    displayFieldError(field, message) {
        // Remove existing error
        this.clearFieldError(field);

        if (message) {
            field.classList.add('error');
            
            const errorElement = document.createElement('div');
            errorElement.className = 'field-error';
            errorElement.textContent = message;
            
            field.parentNode.appendChild(errorElement);
        }
    }

    clearFieldError(field) {
        field.classList.remove('error');
        const errorElement = field.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }
}

// Global functions
window.reloadData = async () => {
    UIComponents.showNotification('Recarregando dados...', 'info');
    await dataController.loadAllData();
};

window.closeModal = (modalId) => {
    UIComponents.closeModal(modalId);
};

window.closeNotification = () => {
    UIComponents.closeNotification();
};

// Application Initialization
class App {
    constructor() {
        this.controllers = {};
        this.init();
    }

    async init() {
        try {
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.start());
            } else {
                this.start();
            }
        } catch (error) {
            console.error('Error initializing app:', error);
            UIComponents.showNotification('Erro ao inicializar aplicação', 'error');
        }
    }

    start() {
        console.log('🚀 FormAcademy - Inicializando aplicação...');

        // Initialize AOS (Animate On Scroll)
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 600,
                easing: 'ease-out',
                once: true,
                offset: 100
            });
        }

        // Initialize controllers
        this.controllers.navigation = new NavigationController();
        this.controllers.data = new DataController();
        this.controllers.filter = new FilterController();
        this.controllers.form = new FormController();

        // Make controllers globally available
        window.dataController = this.controllers.data;
        window.CourseModal = CourseModal;
        window.PreRegistrationModal = PreRegistrationModal;

        // Setup global error handling
        this.setupErrorHandling();

        console.log('✅ FormAcademy - Aplicação inicializada com sucesso!');
    }

    setupErrorHandling() {
        window.addEventListener('error', (e) => {
            console.error('Unhandled error:', e.error);
            UIComponents.showNotification('Ocorreu um erro inesperado', 'error');
        });

        window.addEventListener('unhandledrejection', (e) => {
            console.error('Unhandled promise rejection:', e.reason);
            UIComponents.showNotification('Erro de comunicação com o servidor', 'error');
        });
    }
}

// Start the application
const app = new App();
