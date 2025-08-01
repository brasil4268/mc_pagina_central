# 📚 DOCUMENTAÇÃO DETALHADA - MC FORMAÇÃO

## 🎯 VISÃO GERAL DO SISTEMA

O **MC Formação** é um sistema completo de gestão de centro de formação profissional que integra:
- **Site público** para divulgação e captação de interessados
- **Painel administrativo** para gestão completa do centro
- **Sistema de loja/snack bar** para venda de produtos

---

## 🏗️ ARQUITETURA DO PROJETO

### 📁 ESTRUTURA DE DIRETÓRIOS

```
mc_pagina_central/
├── 📂 app/
│   ├── 📂 Http/Controllers/        # Controllers da aplicação
│   │   ├── Api/                   # Controllers da API REST
│   │   ├── CategoriaController.php # Gestão de categorias
│   │   └── ProdutoController.php   # Gestão de produtos
│   └── 📂 Models/                 # Models Eloquent
│       ├── Centro.php             # Model de centros
│       ├── Curso.php              # Model de cursos
│       ├── Formador.php           # Model de formadores
│       ├── Horario.php            # Model de horários
│       ├── PreInscricao.php       # Model de pré-inscrições
│       ├── Categoria.php          # Model de categorias
│       └── Produto.php            # Model de produtos
├── 📂 database/migrations/        # Migrações do banco de dados
├── 📂 resources/views/            # Templates Blade
│   ├── 📂 layouts/               # Layouts base
│   │   ├── public.blade.php      # Layout do site público
│   │   └── app.blade.php         # Layout do painel admin
│   ├── 📂 public/                # Views públicas
│   │   ├── home.blade.php        # Página inicial
│   │   ├── centros.blade.php     # Lista de centros
│   │   ├── centro-detalhes.blade.php # Detalhes do centro
│   │   ├── sobre.blade.php       # Página sobre
│   │   ├── contactos.blade.php   # Página de contactos
│   │   └── pre-inscricao.blade.php # Formulário de inscrição
│   ├── dashboard.blade.php       # Dashboard administrativo
│   └── 📂 [módulos]/             # Views dos módulos CRUD
├── 📂 routes/
│   ├── web.php                   # Rotas web da aplicação
│   └── api.php                   # Rotas da API REST
└── 📂 public/                    # Assets públicos
    ├── 📂 images/                # Imagens do site
    └── 📂 js/                    # Scripts JavaScript
```

---

## 🎨 LAYOUTS E ESTRUTURA VISUAL

### 🌐 LAYOUT PÚBLICO (`layouts/public.blade.php`)

**Componentes principais:**
- **Top Bar**: Informações de contacto (email, telefone, horário)
- **Header**: Logo MC-COMERCIAL + navegação principal
- **Hero Section**: Chamada para ação principal
- **Footer**: Links, contactos, redes sociais

**Imagens utilizadas:**
- `images/logo.png` - Logo principal (40px altura)
- Imagens da Unsplash para hero sections (600x400px)
- Ícones Font Awesome para interface

**Cores do tema:**
```css
--primary-color: #1e3a8a;      /* Azul principal */
--secondary-color: #334155;    /* Cinza escuro */
--accent-color: #3b82f6;       /* Azul claro */
--light-gray: #f1f5f9;         /* Cinza claro */
```

### 🔧 LAYOUT ADMINISTRATIVO (`layouts/app.blade.php`)

**Componentes principais:**
- **Navbar superior**: Logo + menu do usuário
- **Sidebar**: Menu lateral com navegação modular
- **Main content**: Área de conteúdo dinâmico
- **Alertas**: Sistema de feedback automático

**Cores do painel:**
```css
--primary-color: #1e40af;      /* Azul do painel */
--success-color: #16a34a;      /* Verde sucessos */
--danger-color: #dc2626;       /* Vermelho alertas */
--warning-color: #ca8a04;      /* Amarelo avisos */
```

---

## 📊 ESTRUTURA DO BANCO DE DADOS

### 🎓 MÓDULO EDUCACIONAL

#### Tabela: `cursos`
```sql
id              BIGINT (PK)
nome            VARCHAR(100)        -- Nome do curso
descricao       TEXT               -- Descrição detalhada
programa        TEXT               -- Conteúdo programático
area            VARCHAR(100)       -- Área (Informática, Gestão, etc.)
modalidade      ENUM(presencial,online)
imagem_url      VARCHAR            -- URL da imagem
ativo           BOOLEAN            -- Status do curso
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### Tabela: `centros`
```sql
id              BIGINT (PK)
nome            VARCHAR            -- Nome do centro
localizacao     TEXT              -- Endereço completo
contactos       JSON              -- Array de telefones
email           VARCHAR           -- Email de contacto
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### Tabela: `formadores`
```sql
id              BIGINT (PK)
nome            VARCHAR           -- Nome completo
email           VARCHAR           -- Email do formador
telefone        VARCHAR           -- Telefone de contacto
especializacao  VARCHAR           -- Área de especialização
cv_url          VARCHAR           -- URL do currículo
ativo           BOOLEAN           -- Status ativo/inativo
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### Tabela: `horarios`
```sql
id              BIGINT (PK)
curso_id        BIGINT (FK)       -- Referência ao curso
centro_id       BIGINT (FK)       -- Referência ao centro
dias_semana     JSON              -- Array de dias
hora_inicio     TIME              -- Hora de início
hora_fim        TIME              -- Hora de término
data_inicio     DATE              -- Data de início da turma
data_fim        DATE              -- Data de término
max_alunos      INTEGER           -- Limite de vagas
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### Tabela: `pre_inscricoes`
```sql
id              BIGINT (PK)
nome_completo   VARCHAR           -- Nome do interessado
email           VARCHAR           -- Email de contacto
telefone        VARCHAR           -- Telefone
curso_id        BIGINT (FK)       -- Curso de interesse
centro_id       BIGINT (FK)       -- Centro preferido
observacoes     TEXT              -- Observações adicionais
status          ENUM(pendente,confirmado,cancelado)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### 🏪 MÓDULO LOJA/SNACK BAR

#### Tabela: `categorias`
```sql
id              BIGINT (PK)
nome            VARCHAR           -- Nome da categoria
descricao       TEXT              -- Descrição
tipo            ENUM(loja,snack)  -- Tipo de categoria
ativo           BOOLEAN           -- Status ativo/inativo
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### Tabela: `produtos`
```sql
id              BIGINT (PK)
categoria_id    BIGINT (FK)       -- Referência à categoria
nome            VARCHAR           -- Nome do produto
descricao       TEXT              -- Descrição detalhada
preco           DECIMAL(8,2)      -- Preço do produto
imagem_url      VARCHAR           -- URL da imagem
disponivel      BOOLEAN           -- Disponibilidade
em_destaque     BOOLEAN           -- Produto em destaque
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### 🔗 TABELAS DE RELACIONAMENTO

#### `centro_curso` (Many-to-Many)
```sql
centro_id       BIGINT (FK)
curso_id        BIGINT (FK)
preco           DECIMAL(8,2)      -- Preço específico por centro
duracao         INTEGER           -- Duração em horas
data_arranque   DATE              -- Data de início
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### `curso_formador` (Many-to-Many)
```sql
curso_id        BIGINT (FK)
formador_id     BIGINT (FK)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### `centro_formador` (Many-to-Many)
```sql
centro_id       BIGINT (FK)
formador_id     BIGINT (FK)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

---

## 🔌 API REST

### 📋 ENDPOINTS PRINCIPAIS

#### Centros de Formação
```
GET    /api/centros           # Listar todos os centros
POST   /api/centros           # Criar novo centro
GET    /api/centros/{id}      # Exibir centro específico
PUT    /api/centros/{id}      # Atualizar centro
DELETE /api/centros/{id}      # Excluir centro
```

#### Cursos
```
GET    /api/cursos            # Listar todos os cursos
POST   /api/cursos            # Criar novo curso
GET    /api/cursos/{id}       # Exibir curso específico
PUT    /api/cursos/{id}       # Atualizar curso
DELETE /api/cursos/{id}       # Excluir curso
```

#### Pré-inscrições
```
GET    /api/pre-inscricoes    # Listar pré-inscrições (admin)
POST   /api/pre-inscricoes    # Criar pré-inscrição (público)
GET    /api/pre-inscricoes/{id} # Exibir específica
PUT    /api/pre-inscricoes/{id} # Atualizar/aprovar (admin)
DELETE /api/pre-inscricoes/{id} # Excluir
```

#### Produtos & Categorias
```
GET    /api/categorias        # Listar categorias (filtros: tipo)
GET    /api/produtos          # Listar produtos (filtros: categoria)
GET    /api/produtos/em-destaque # Produtos em destaque
GET    /api/categorias/{categoria}/produtos # Produtos por categoria
```

---

## 🎯 FUNCIONALIDADES PRINCIPAIS

### 🌐 SITE PÚBLICO

#### Página Inicial (`public/home.blade.php`)
- **Hero Section**: Chamada principal com botões de ação
- **Estatísticas**: Cards com números de alunos, cursos, centros
- **Cursos em Destaque**: Grid de cursos populares
- **Sobre a Empresa**: Informações institucionais
- **Depoimentos**: Feedback de ex-alunos
- **Contactos**: Formulário e informações

#### Centros (`public/centros.blade.php`)
- **Lista de Centros**: Cards com informações básicas
- **Filtros**: Por localização ou tipo de curso
- **Mapa Interativo**: Localização dos centros
- **Cursos por Centro**: Lista de cursos disponíveis

#### Pré-inscrição (`public/pre-inscricao.blade.php`)
- **Formulário Completo**: Dados pessoais e preferências
- **Seleção de Curso**: Dropdown dinâmico
- **Seleção de Centro**: Baseado no curso escolhido
- **Validação Frontend**: JavaScript + backend

### 🔧 PAINEL ADMINISTRATIVO

#### Dashboard (`dashboard.blade.php`)
- **Cards de Estatísticas**: Totais dinâmicos via AJAX
- **Últimas Pré-inscrições**: Tabela com status
- **Estatísticas Rápidas**: Gráficos de modalidades
- **Ações Rápidas**: Links para criar novos registos

#### Módulos CRUD
- **Interface DataTables**: Todas as listagens
- **Formulários Modais**: Criação e edição
- **Confirmações SweetAlert**: Para exclusões
- **Feedback Visual**: Alertas de sucesso/erro

---

## 🛠️ TECNOLOGIAS UTILIZADAS

### Backend
- **Laravel 10**: Framework PHP principal
- **Eloquent ORM**: Para relacionamentos e queries
- **Blade Templating**: Engine de templates
- **Laravel Sanctum**: Autenticação API (futuro)

### Frontend
- **Bootstrap 5.3**: Framework CSS responsivo
- **Font Awesome 6.4**: Biblioteca de ícones
- **jQuery 3.7**: Manipulação DOM e AJAX
- **DataTables 1.13**: Tabelas interativas
- **SweetAlert2**: Alertas elegantes

### Base de Dados
- **MySQL/MariaDB**: Sistema de gestão de BD
- **Migrações Laravel**: Controle de versão da BD

---

## 📱 RESPONSIVIDADE

### Breakpoints Bootstrap
- **Mobile**: < 768px
- **Tablet**: 768px - 992px  
- **Desktop**: > 992px

### Adaptações Mobile
- **Sidebar**: Menu hambúrguer colapsável
- **Tabelas**: Scroll horizontal automático
- **Cards**: Stack vertical em mobile
- **Navegação**: Menu móvel otimizado

---

## 🔒 SEGURANÇA

### Validações
- **CSRF Protection**: Tokens em formulários
- **Input Validation**: Laravel Validation Rules
- **SQL Injection**: Eloquent ORM protegido
- **XSS Protection**: Blade escaping automático

### Autenticação (Futuro)
- **Laravel Sanctum**: Para APIs
- **Middleware Auth**: Proteção de rotas
- **Roles & Permissions**: Controle de acesso

---

## 🚀 DEPLOY E AMBIENTE

### Requisitos do Servidor
- **PHP**: >= 8.1
- **MySQL**: >= 5.7
- **Composer**: Para dependências PHP
- **Node.js**: Para assets (opcional)

### Configuração
1. Clone do repositório
2. `composer install`
3. Configurar `.env`
4. `php artisan migrate`
5. `php artisan serve`

---

## 📝 MANUTENÇÃO E ATUALIZAÇÕES

### Logs do Sistema
- **Laravel Logs**: `storage/logs/laravel.log`
- **Web Server Logs**: Configuração do servidor
- **Database Logs**: Queries e erros

### Backup
- **Database**: Scripts de backup regulares
- **Files**: Backup de uploads e imagens
- **Code**: Controle de versão Git

---

## 🎨 PERSONALIZAÇÃO DE IMAGENS

### Logos e Marca
- **Logo Principal**: `public/images/logo.png` (40px altura)
- **Favicon**: `public/images/favicon.ico`
- **Logo Footer**: Mesma imagem redimensionada

### Imagens de Curso
- **Dimensões**: 600x400px recomendado
- **Formato**: JPG ou PNG
- **Otimização**: Compressão para web

### Imagens de Produtos
- **Dimensões**: 300x300px (quadrado)
- **Formato**: JPG ou PNG  
- **Background**: Branco preferível

### Hero Images
- **Dimensões**: 1200x600px
- **Formato**: JPG otimizado
- **Tema**: Profissional/educativo

---

Este sistema foi projetado para ser **escalável**, **modular** e **fácil de manter**, seguindo as melhores práticas do Laravel e desenvolvimento web moderno.
