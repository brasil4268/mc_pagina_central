# 🚀 Guia de Deploy para Produção - MC Formação

## ✅ Pré-requisitos de Produção

### 1. Servidor de Produção
- **PHP 8.1+** com extensões: `pdo`, `pdo_mysql`, `bcmath`, `ctype`, `json`, `fileinfo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `curl`, `zip`
- **MySQL/MariaDB 8.0+**
- **Nginx** ou **Apache**
- **Composer**
- **Node.js 16+** e **npm**
- **SSL Certificate** (recomendado)

### 2. Configurações de Segurança
- Firewall configurado (portas 80, 443, 22)
- Backup automático da base de dados
- Monitorização de logs

## 🔧 Passos para Deploy

### 1. Preparar Ficheiros
```bash
# Comprimir projeto (excluindo ficheiros desnecessários)
tar -czf mc_formacao.tar.gz \
  --exclude='node_modules' \
  --exclude='.git' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  ./
```

### 2. Upload para Servidor
```bash
# Upload via SCP (substitua pelos seus dados)
scp mc_formacao.tar.gz user@servidor:/var/www/
```

### 3. Configurar no Servidor
```bash
# Extrair ficheiros
cd /var/www/
tar -xzf mc_formacao.tar.gz
mv mc_pagina_central/* /var/www/html/
cd /var/www/html/

# Instalar dependências PHP
composer install --optimize-autoloader --no-dev

# Instalar dependências Node.js
npm install
npm run build

# Configurar permissões
chown -R www-data:www-data /var/www/html/
chmod -R 755 /var/www/html/
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 4. Configurar Base de Dados
```bash
# Criar base de dados
mysql -u root -p
CREATE DATABASE mc_formacao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mc_user'@'localhost' IDENTIFIED BY 'senha_forte_aqui';
GRANT ALL PRIVILEGES ON mc_formacao.* TO 'mc_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5. Configurar .env de Produção
```bash
# Copiar e editar configuração
cp .env.example .env
nano .env
```

**Configuração .env de Produção:**
```env
APP_NAME="MC Formação"
APP_ENV=production
APP_KEY=base64:GERAR_NOVA_CHAVE_AQUI
APP_DEBUG=false
APP_URL=https://seudominio.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mc_formacao
DB_USERNAME=mc_user
DB_PASSWORD=senha_forte_aqui

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Email (configurar conforme seu provedor)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 6. Executar Migrações e Seeders
```bash
# Gerar chave da aplicação
php artisan key:generate

# Executar migrações
php artisan migrate

# Executar seeders (dados iniciais)
php artisan db:seed --class=CursoSeeder
php artisan db:seed --class=CentroSeeder
php artisan db:seed --class=FormadorSeeder
php artisan db:seed --class=CategoriaSeeder
php artisan db:seed --class=ProdutoSeeder

# Limpar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 7. Configurar Nginx (Recomendado)
```nginx
# /etc/nginx/sites-available/mc_formacao
server {
    listen 80;
    server_name seudominio.com www.seudominio.com;
    root /var/www/html/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 8. Configurar SSL com Certbot
```bash
# Instalar Certbot
apt install certbot python3-certbot-nginx

# Obter certificado SSL
certbot --nginx -d seudominio.com -d www.seudominio.com

# Testar renovação automática
certbot renew --dry-run
```

## 🔐 Configurações de Segurança Adicionais

### 1. Configurar Firewall
```bash
# UFW (Ubuntu)
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
```

### 2. Configurar Backup Automático
```bash
# Script de backup (/usr/local/bin/backup_mc_formacao.sh)
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/mc_formacao"
DB_NAME="mc_formacao"
DB_USER="mc_user"
DB_PASS="senha_forte_aqui"

# Criar diretório se não existir
mkdir -p $BACKUP_DIR

# Backup da base de dados
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Backup dos ficheiros
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/html/storage/app/

# Remover backups antigos (manter últimos 7 dias)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

# Crontab para execução diária às 2:00
# 0 2 * * * /usr/local/bin/backup_mc_formacao.sh
```

## 📊 Monitorização e Logs

### 1. Logs do Laravel
```bash
# Localização dos logs
tail -f /var/www/html/storage/logs/laravel.log

# Configurar rotação de logs
# Adicionar ao crontab
0 0 * * * cd /var/www/html && php artisan log:clear
```

### 2. Logs do Nginx
```bash
# Access logs
tail -f /var/log/nginx/access.log

# Error logs
tail -f /var/log/nginx/error.log
```

## ⚡ Optimizações de Performance

### 1. Configurar Cache Redis (Opcional)
```bash
# Instalar Redis
apt install redis-server

# Actualizar .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### 2. Configurar Queue (Opcional)
```bash
# Para processamento de tarefas em background
QUEUE_CONNECTION=database

# Executar worker
php artisan queue:work --daemon
```

## 🚨 Checklist Final

- [ ] ✅ Todas as migrações executadas
- [ ] ✅ Seeders executados (dados de teste)
- [ ] ✅ .env configurado para produção
- [ ] ✅ APP_DEBUG=false
- [ ] ✅ SSL certificado instalado
- [ ] ✅ Permissões de ficheiros correctas
- [ ] ✅ Firewall configurado
- [ ] ✅ Backup automático configurado
- [ ] ✅ Logs monitorizados
- [ ] ✅ Cache optimizado
- [ ] ✅ DNS apontando para servidor

## 🛠️ Comandos Úteis para Manutenção

```bash
# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-optimizar para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Verificar estado dos serviços
systemctl status nginx
systemctl status mysql
systemctl status php8.1-fpm

# Actualizar aplicação
git pull origin main
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📧 Suporte e Contacto

Para questões relacionadas com o deploy ou funcionamento da aplicação:
- Documentação: Este ficheiro
- Logs: `/var/www/html/storage/logs/laravel.log`
- Configuração: `/var/www/html/.env`

---

**✅ Sistema Pronto para Produção!**

O sistema MC Formação está agora configurado e pronto para uso em ambiente de produção com todas as funcionalidades operacionais:
- Dashboard administrativo completo
- API funcional para todas as entidades
- Site público optimizado
- DataTables com tradução portuguesa
- Sistema de CRUD completo
