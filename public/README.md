# FormAcademy - Interface Moderna

Uma interface moderna e animada para o sistema de gestão de centro de formação.

## 🚀 Funcionalidades

### Site Público (`index-new.html`)
- **Design moderno** com animações suaves e gradientes
- **Hero section** animada com partículas flutuantes
- **Visualização de centros** com cards interativos
- **Catálogo de cursos** com filtros avançados
- **Detalhes dos cursos** em modals responsivos
- **Sistema de pré-inscrição** funcional
- **Responsivo** para todos os dispositivos
- **Animações AOS** (Animate On Scroll)

### Painel Administrativo (`admin-new.html`)
- **Dashboard** com estatísticas em tempo real
- **Sidebar moderna** com navegação fluida
- **CRUD completo** para todos os recursos:
  - Centros de formação
  - Cursos
  - Formadores
  - Horários
  - Pré-inscrições
- **Tabelas interativas** com busca e filtros
- **Modais dinâmicos** para criação/edição
- **Sistema de contactos múltiplos** com botão +/-
- **Validação de formulários** em tempo real
- **Notificações** modernas
- **Design responsivo**

## 📁 Arquivos Criados

### Interface Pública
- `index-new.html` - Página principal moderna
- `modern-styles.css` - Estilos modernos do site público
- `modern-script.js` - JavaScript avançado com funcionalidades completas

### Interface Administrativa
- `admin-new.html` - Painel administrativo moderno
- `admin-modern-styles.css` - Estilos do painel admin
- `admin-modern-script.js` - JavaScript do painel administrativo

### Utilitários
- `home.php` - Redirecionamento para a nova interface
- `README.md` - Este arquivo de documentação

## 🎨 Características da Interface

### Design System
- **Cores primárias**: Gradientes modernos azul/roxo
- **Tipografia**: Inter font family
- **Animações**: Transições suaves e efeitos hover
- **Responsividade**: Mobile-first design
- **Acessibilidade**: Contraste adequado e navegação por teclado

### Funcionalidades Avançadas
- **Loading states** com spinners animados
- **Error handling** robusto com retry automático
- **State management** centralizado
- **Debouncing** para otimização de performance
- **Lazy loading** de dados
- **Caching** inteligente

### Sistema de Contactos
- **Múltiplos contactos** por centro/formador
- **Botão +** para adicionar novos campos
- **Botão -** para remover campos
- **Validação** de formato de telefone
- **Armazenamento JSON** no backend

## 🔧 Como Usar

### Acesso às Interfaces
1. **Site Público**: `http://localhost:8000/index-new.html`
2. **Painel Admin**: `http://localhost:8000/admin-new.html`
3. **Redirecionamento**: `http://localhost:8000/home.php`

### Navegação
- **Site para Admin**: Botão "Admin" no canto superior direito
- **Admin para Site**: Botão "Site Público" na sidebar
- **Navegação fluida**: Links na navbar e sidebar

### Funcionalidades do Site Público
1. **Visualizar centros**: Seção "Nossos Centros"
2. **Explorar cursos**: Seção "Cursos Disponíveis"
3. **Filtrar cursos**: Por centro, área e modalidade
4. **Ver detalhes**: Clique em qualquer curso
5. **Fazer pré-inscrição**: Botão no modal do curso

### Funcionalidades do Admin
1. **Dashboard**: Estatísticas e gráficos
2. **Gestão**: CRUD completo para todos os recursos
3. **Busca**: Campo de busca em cada seção
4. **Filtros**: Dropdowns para filtrar dados
5. **Ações rápidas**: Botões de criação no dashboard

## 🛠️ Tecnologias Utilizadas

### Frontend
- **HTML5** com semântica moderna
- **CSS3** com variáveis customizadas e flexbox/grid
- **JavaScript ES6+** com classes e async/await
- **Font Awesome** para ícones
- **AOS Library** para animações on scroll
- **Inter Font** para tipografia moderna

### Funcionalidades JavaScript
- **Fetch API** para comunicação com backend
- **Promise.all** para carregamento paralelo
- **IntersectionObserver** para lazy loading
- **LocalStorage** para cache básico
- **Event delegation** para performance
- **Error boundaries** para robustez

### Recursos CSS
- **CSS Grid** e **Flexbox** para layout
- **CSS Custom Properties** para theming
- **CSS Animations** e **Transitions**
- **Media queries** para responsividade
- **Backdrop-filter** para efeitos modernos

## 📊 Performance

### Otimizações
- **Debounced search** (300ms delay)
- **Lazy loading** de seções
- **Efficient DOM manipulation**
- **CSS-only animations** quando possível
- **Minimal dependencies**

### Compatibilidade
- **Chrome/Safari/Firefox** - Suporte completo
- **Edge** - Suporte completo
- **Mobile browsers** - Otimizado
- **Tablets** - Layout adaptado

## 🎯 Próximos Passos

### Melhorias Sugeridas
1. **PWA** - Service workers para offline
2. **Dark mode** - Tema escuro alternativo
3. **Internationalization** - Suporte multi-idioma
4. **Charts** - Gráficos interativos no dashboard
5. **Export** - Funcionalidade de exportar dados

### Customização
- **Cores**: Modificar variáveis CSS em `:root`
- **Animações**: Ajustar durações em `--transition-*`
- **Layout**: Modificar breakpoints responsivos
- **Funcionalidades**: Adicionar novos módulos JavaScript

## 📞 Suporte

Para dúvidas ou melhorias na interface:
1. Verificar console do navegador para erros
2. Testar conectividade com API Laravel
3. Validar estrutura de dados retornada
4. Confirmar permissões CORS se necessário

---

**Desenvolvido com 💙 para FormAcademy**
