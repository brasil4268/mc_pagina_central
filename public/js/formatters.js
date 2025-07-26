// Data Formatters - Centro de Formação
// ====================================

/**
 * Utilitários para formatação de dados vindos do backend
 */
class DataFormatters {

    /**
     * Formatar contactos vindos do backend
     * @param {Array|string|Object} contacts 
     * @returns {Object} Contactos formatados
     */
    static formatContacts(contacts) {
        if (!contacts) return { phones: [], displayText: 'Sem contacto' };

        let phoneArray = [];
        
        if (Array.isArray(contacts)) {
            phoneArray = contacts;
        } else if (typeof contacts === 'string') {
            try {
                const parsed = JSON.parse(contacts);
                phoneArray = Array.isArray(parsed) ? parsed : [parsed];
            } catch {
                phoneArray = [contacts];
            }
        } else if (typeof contacts === 'object') {
            phoneArray = Object.values(contacts);
        }

        // Garantir que são strings e formatá-los
        const formattedPhones = phoneArray
            .filter(phone => phone)
            .map(phone => this.formatPhoneNumber(phone.toString()));

        // Texto para exibição
        let displayText = 'Sem contacto';
        if (formattedPhones.length > 0) {
            displayText = formattedPhones[0];
            if (formattedPhones.length > 1) {
                displayText += ` (+${formattedPhones.length - 1})`;
            }
        }

        return {
            phones: formattedPhones,
            displayText: displayText,
            count: formattedPhones.length
        };
    }

    /**
     * Formatar número de telefone angolano
     * @param {string} phone 
     * @returns {string} Telefone formatado
     */
    static formatPhoneNumber(phone) {
        if (!phone) return '';
        
        // Remove espaços e caracteres especiais
        const cleanPhone = phone.replace(/\D/g, '');
        
        // Formato angolano: 9XX XXX XXX
        if (cleanPhone.length === 9 && cleanPhone.startsWith('9')) {
            return `${cleanPhone.slice(0, 3)} ${cleanPhone.slice(3, 6)} ${cleanPhone.slice(6)}`;
        }
        
        return phone; // Retorna original se não conseguir formatar
    }

    /**
     * Formatar preço em Kwanzas
     * @param {number|string} price 
     * @returns {string} Preço formatado
     */
    static formatPrice(price) {
        if (!price && price !== 0) return 'Preço não definido';
        
        const numPrice = parseFloat(price);
        if (isNaN(numPrice)) return 'Preço inválido';

        return new Intl.NumberFormat('pt-AO', {
            style: 'currency',
            currency: 'AOA',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(numPrice);
    }

    /**
     * Formatar data/hora do Laravel
     * @param {string} dateString 
     * @returns {string} Data formatada
     */
    static formatDateTime(dateString) {
        if (!dateString) return 'Data não definida';

        try {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('pt-PT', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        } catch {
            return 'Data inválida';
        }
    }

    /**
     * Formatar apenas data
     * @param {string} dateString 
     * @returns {string} Data formatada
     */
    static formatDate(dateString) {
        if (!dateString) return 'Data não definida';

        try {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('pt-PT', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            }).format(date);
        } catch {
            return 'Data inválida';
        }
    }

    /**
     * Formatar horário (HH:MM)
     * @param {string} timeString 
     * @returns {string} Horário formatado
     */
    static formatTime(timeString) {
        if (!timeString) return '--:--';

        // Se já está no formato HH:MM, retorna
        if (/^\d{2}:\d{2}$/.test(timeString)) {
            return timeString;
        }

        // Tenta converter de outros formatos
        try {
            const date = new Date(`1970-01-01T${timeString}`);
            return date.toLocaleTimeString('pt-PT', {
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch {
            return timeString || '--:--';
        }
    }

    /**
     * Formatar status de pré-inscrição
     * @param {string} status 
     * @returns {Object} Status formatado com classe CSS
     */
    static formatStatus(status) {
        const statusMap = {
            'pendente': { text: 'Pendente', class: 'status-pendente', icon: 'fas fa-clock' },
            'confirmado': { text: 'Confirmado', class: 'status-confirmado', icon: 'fas fa-check-circle' },
            'cancelado': { text: 'Cancelado', class: 'status-cancelado', icon: 'fas fa-times-circle' },
            'ativo': { text: 'Ativo', class: 'status-ativo', icon: 'fas fa-check' },
            'inativo': { text: 'Inativo', class: 'status-inativo', icon: 'fas fa-times' }
        };

        return statusMap[status?.toLowerCase()] || { 
            text: status || 'Indefinido', 
            class: 'status-indefinido', 
            icon: 'fas fa-question' 
        };
    }

    /**
     * Formatar modalidade de curso
     * @param {string} modalidade 
     * @returns {Object} Modalidade formatada
     */
    static formatModalidade(modalidade) {
        const modalidadeMap = {
            'presencial': { text: 'Presencial', class: 'modalidade-presencial', icon: 'fas fa-building' },
            'online': { text: 'Online', class: 'modalidade-online', icon: 'fas fa-laptop' }
        };

        return modalidadeMap[modalidade?.toLowerCase()] || { 
            text: modalidade || 'Não definida', 
            class: 'modalidade-indefinida', 
            icon: 'fas fa-question' 
        };
    }

    /**
     * Formatar período do dia
     * @param {string} periodo 
     * @returns {Object} Período formatado
     */
    static formatPeriodo(periodo) {
        const periodoMap = {
            'manhã': { text: 'Manhã', class: 'periodo-manha', icon: 'fas fa-sun' },
            'tarde': { text: 'Tarde', class: 'periodo-tarde', icon: 'fas fa-cloud-sun' },
            'noite': { text: 'Noite', class: 'periodo-noite', icon: 'fas fa-moon' }
        };

        return periodoMap[periodo?.toLowerCase()] || { 
            text: periodo || 'Não definido', 
            class: 'periodo-indefinido', 
            icon: 'fas fa-clock' 
        };
    }

    /**
     * Formatar dia da semana
     * @param {string} dia 
     * @returns {string} Dia formatado
     */
    static formatDiaSemana(dia) {
        if (!dia) return 'Dia não definido';
        
        const diasMap = {
            'segunda': 'Segunda-feira',
            'terça': 'Terça-feira', 
            'terca': 'Terça-feira',
            'quarta': 'Quarta-feira',
            'quinta': 'Quinta-feira',
            'sexta': 'Sexta-feira',
            'sábado': 'Sábado',
            'sabado': 'Sábado',
            'domingo': 'Domingo'
        };

        return diasMap[dia.toLowerCase()] || dia;
    }

    /**
     * Truncar texto longo
     * @param {string} text 
     * @param {number} maxLength 
     * @returns {string} Texto truncado
     */
    static truncateText(text, maxLength = 100) {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength).trim() + '...';
    }

    /**
     * Sanitizar HTML
     * @param {string} str 
     * @returns {string} String sanitizada
     */
    static sanitizeHTML(str) {
        if (!str) return '';
        const temp = document.createElement('div');
        temp.textContent = str;
        return temp.innerHTML;
    }

    /**
     * Formatar email para exibição
     * @param {string} email 
     * @returns {string} Email formatado
     */
    static formatEmail(email) {
        if (!email) return 'Email não informado';
        return email.toLowerCase().trim();
    }

    /**
     * Formatar URL para exibição
     * @param {string} url 
     * @returns {string} URL formatada
     */
    static formatURL(url) {
        if (!url) return '';
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            return 'https://' + url;
        }
        return url;
    }

    /**
     * Formatar dados completos de centro
     * @param {Object} centro 
     * @returns {Object} Centro formatado
     */
    static formatCentro(centro) {
        if (!centro) return null;

        const contacts = this.formatContacts(centro.contactos);
        
        return {
            ...centro,
            contacts: contacts,
            email: this.formatEmail(centro.email),
            nome: this.sanitizeHTML(centro.nome),
            localizacao: this.sanitizeHTML(centro.localizacao),
            created_at_formatted: this.formatDateTime(centro.created_at),
            updated_at_formatted: this.formatDateTime(centro.updated_at)
        };
    }

    /**
     * Formatar dados completos de curso
     * @param {Object} curso 
     * @returns {Object} Curso formatado
     */
    static formatCurso(curso) {
        if (!curso) return null;

        const modalidade = this.formatModalidade(curso.modalidade);
        const status = this.formatStatus(curso.ativo ? 'ativo' : 'inativo');

        return {
            ...curso,
            nome: this.sanitizeHTML(curso.nome),
            descricao: this.sanitizeHTML(curso.descricao),
            programa: this.sanitizeHTML(curso.programa),
            duracao: this.sanitizeHTML(curso.duracao),
            area: this.sanitizeHTML(curso.area),
            preco_formatted: this.formatPrice(curso.preco),
            modalidade_formatted: modalidade,
            status_formatted: status,
            imagem_url: this.formatURL(curso.imagem_url),
            created_at_formatted: this.formatDateTime(curso.created_at),
            updated_at_formatted: this.formatDateTime(curso.updated_at),
            descricao_short: this.truncateText(curso.descricao, 120),
            programa_short: this.truncateText(curso.programa, 150)
        };
    }

    /**
     * Formatar dados completos de formador
     * @param {Object} formador 
     * @returns {Object} Formador formatado
     */
    static formatFormador(formador) {
        if (!formador) return null;

        const contacts = this.formatContacts(formador.contactos);

        return {
            ...formador,
            nome: this.sanitizeHTML(formador.nome),
            email: this.formatEmail(formador.email),
            especialidade: this.sanitizeHTML(formador.especialidade),
            bio: this.sanitizeHTML(formador.bio),
            contacts: contacts,
            foto_url: this.formatURL(formador.foto_url),
            created_at_formatted: this.formatDateTime(formador.created_at),
            updated_at_formatted: this.formatDateTime(formador.updated_at),
            bio_short: this.truncateText(formador.bio, 100),
            initials: this.getInitials(formador.nome)
        };
    }

    /**
     * Formatar dados completos de horário
     * @param {Object} horario 
     * @returns {Object} Horário formatado
     */
    static formatHorario(horario) {
        if (!horario) return null;

        const periodo = this.formatPeriodo(horario.periodo);

        return {
            ...horario,
            dia_semana_formatted: this.formatDiaSemana(horario.dia_semana),
            periodo_formatted: periodo,
            hora_inicio_formatted: this.formatTime(horario.hora_inicio),
            hora_fim_formatted: this.formatTime(horario.hora_fim),
            created_at_formatted: this.formatDateTime(horario.created_at),
            updated_at_formatted: this.formatDateTime(horario.updated_at),
            horario_completo: `${this.formatDiaSemana(horario.dia_semana)} - ${periodo.text} (${this.formatTime(horario.hora_inicio)} às ${this.formatTime(horario.hora_fim)})`
        };
    }

    /**
     * Formatar dados completos de pré-inscrição
     * @param {Object} preInscricao 
     * @returns {Object} Pré-inscrição formatada
     */
    static formatPreInscricao(preInscricao) {
        if (!preInscricao) return null;

        const contacts = this.formatContacts(preInscricao.contactos);
        const status = this.formatStatus(preInscricao.status);

        return {
            ...preInscricao,
            nome_completo: this.sanitizeHTML(preInscricao.nome_completo),
            email: this.formatEmail(preInscricao.email),
            observacoes: this.sanitizeHTML(preInscricao.observacoes),
            contacts: contacts,
            status_formatted: status,
            created_at_formatted: this.formatDateTime(preInscricao.created_at),
            updated_at_formatted: this.formatDateTime(preInscricao.updated_at),
            observacoes_short: this.truncateText(preInscricao.observacoes, 80)
        };
    }

    /**
     * Obter iniciais do nome
     * @param {string} nome 
     * @returns {string} Iniciais
     */
    static getInitials(nome) {
        if (!nome) return 'NN';
        
        const words = nome.trim().split(' ');
        if (words.length === 1) {
            return words[0].substring(0, 2).toUpperCase();
        }
        
        return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
    }

    /**
     * Formatar lista de dados
     * @param {Array} data 
     * @param {string} type 
     * @returns {Array} Lista formatada
     */
    static formatList(data, type) {
        if (!Array.isArray(data)) return [];

        const formatters = {
            'centros': this.formatCentro.bind(this),
            'cursos': this.formatCurso.bind(this),
            'formadores': this.formatFormador.bind(this),
            'horarios': this.formatHorario.bind(this),
            'pre-inscricoes': this.formatPreInscricao.bind(this)
        };

        const formatter = formatters[type];
        if (!formatter) return data;

        return data.map(item => formatter(item)).filter(Boolean);
    }
}

// Tornar disponível globalmente
window.DataFormatters = DataFormatters;

// Para compatibilidade com módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DataFormatters;
}
