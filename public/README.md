# 🚀 EduGest - Sistema de Gestão de Centros e Cursos

## 📋 Instruções para Resolver Problemas de Conectividade

### 1. 🔧 Verificações Básicas

#### Servidor Laravel
1. **Certifique-se que o servidor Laravel está rodando:**
   ```bash
   php artisan serve
   ```
   O servidor deve estar rodando em `http://127.0.0.1:8000` ou `http://localhost:8000`

2. **Verifique as rotas da API:**
   ```bash
   php artisan route:list --path=api
   ```

#### Base de Dados
3. **Execute as migrações:**
   ```bash
   php artisan migrate
   ```

4. **Opcional - Seed com dados de teste:**
   ```bash
   php artisan db:seed
   ```

### 2. 🧪 Teste de Conectividade

Abra o arquivo `test.html` no navegador para verificar se a API está funcionando:
- `http://localhost:8000/test.html` (ou onde estiver servindo)

Este arquivo testará:
- ✅ Conexão básica com o servidor
- ✅ GET /api/centros
- ✅ GET /api/cursos  
- ✅ POST /api/centros (criar)

### 3. 🔗 Estrutura de URLs

#### Se estiver usando `php artisan serve`:
- Frontend: `http://127.0.0.1:8000/index.html`
- API: `http://127.0.0.1:8000/api/`

#### Se estiver usando um servidor web:
- Certifique-se que o diretório `public` seja a raiz do servidor

### 4. 📁 Arquivos Criados

```
public/
├── css/
│   └── style.css          # Estilos modernos
├── js/
│   └── app.js             # Framework JavaScript
├── index.html             # Página inicial
├── login.html             # Tela de login
├── centros.html           # Gestão de centros
├── cursos.html            # Gestão de cursos
├── dashboard.html         # Dashboard admin
├── test.html              # Teste de conectividade
└── README.md              # Este arquivo
```

### 5. 🐛 Problemas Comuns e Soluções

#### ❌ "Erro de conexão. Verifique se o servidor está rodando"
**Solução:** 
- Verifique se `php artisan serve` está rodando
- Teste acessando `http://127.0.0.1:8000/api/centros` diretamente no navegador

#### ❌ "CORS Error" 
**Solução:**
- O arquivo `config/cors.php` já está configurado corretamente
- Reinicie o servidor: `php artisan serve`

#### ❌ "404 Not Found" nas rotas da API
**Solução:**
- Verifique se as rotas estão registradas: `php artisan route:list`
- Certifique-se que os controllers existem em `app/Http/Controllers/Api/`

#### ❌ Dados não aparecem na interface
**Solução:**
1. Abra o Console do navegador (F12)
2. Verifique se há erros JavaScript
3. Use `test.html` para diagnosticar problemas da API

### 6. 🔑 Credenciais de Login

- **Email:** `admin@admin.com`
- **Senha:** `admin123`

### 7. 🌐 Navegação

1. **Página Inicial:** `index.html` - Visão geral do sistema
2. **Login:** `login.html` - Autenticação (simulada)
3. **Centros:** `centros.html` - CRUD completo de centros
4. **Cursos:** `cursos.html` - CRUD completo de cursos  
5. **Dashboard:** `dashboard.html` - Painel administrativo
6. **Teste:** `test.html` - Diagnóstico de problemas

### 8. 🔍 Debug e Troubleshooting

#### Console do Navegador (F12):
- Verifique erros JavaScript na aba "Console"
- Monitore requisições HTTP na aba "Network"

#### Logs do Laravel:
```bash
tail -f storage/logs/laravel.log
```

#### Teste Manual da API:
```bash
# Testar GET centros
curl http://127.0.0.1:8000/api/centros

# Testar POST centro
curl -X POST http://127.0.0.1:8000/api/centros \
  -H "Content-Type: application/json" \
  -d '{"nome":"Teste","localizacao":"Luanda","contactos":"923456789"}'
```

### 9. ⚡ Funcionalidades Implementadas

#### ✅ Sistema Completo de CRUD
- **Centros:** Criar, listar, editar, excluir
- **Cursos:** Criar, listar, editar, excluir com filtros avançados

#### ✅ Interface Moderna
- Design responsivo para mobile/desktop
- Animações suaves
- Tema escuro opcional
- Loading states

#### ✅ Validações
- Telefones angolanos (9XXXXXXXX)
- Emails válidos
- Campos obrigatórios
- Feedback visual de erros

#### ✅ Recursos Avançados
- Sistema de busca em tempo real
- Filtros por múltiplos campos
- Exportação de dados (CSV/JSON)
- Dashboard com estatísticas
- Gráficos interativos

### 10. 📞 Suporte

Se ainda tiver problemas:
1. Execute `test.html` e capture os logs
2. Verifique o console do navegador
3. Verifique os logs do Laravel
4. Confirme que todas as migrações foram executadas

---

## 🎯 Próximos Passos

1. Abra `test.html` para verificar conectividade
2. Faça login com as credenciais fornecidas
3. Teste as funcionalidades de CRUD
4. Explore o dashboard e relatórios

**O sistema está totalmente funcional e pronto para uso!** 🚀
