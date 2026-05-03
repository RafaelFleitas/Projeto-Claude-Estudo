# Sistema de Gestão de Contratos e Relatórios Criado Com Claude

Aplicação web para gerenciamento de contratos, geração de relatórios e rastreamento de auditoria, construída com Laravel 13 e React 19. - OBJETO DE ESTUDO EM CLAUDE IA

## Funcionalidades

- **Contratos**: CRUD completo com rastreamento de status, geração de PDFs com QR Code e endpoint público de validação
- **Relatórios**: Geração assíncrona (via filas) nos formatos Excel, CSV e PDF, com envio para o Telegram
- **Auditoria**: Trilha completa de alterações com rastreamento por IP, usuário e tipo de evento
- **Painel Admin**: Gerenciamento de usuários com controle de papéis (Admin/Usuário)
- **Dashboard**: Estatísticas de contratos, tendências mensais e lista de contratos recentes
- **Autenticação**: Login, registro, 2FA (TOTP), verificação de e-mail e redefinição de senha via Laravel Fortify

## Stack

**Backend:** PHP 8.3, Laravel 13, Inertia.js v3 (Laravel), Laravel Fortify, Laravel Wayfinder

**Frontend:** React 19, TypeScript 5.7, Tailwind CSS v4, Radix UI, Inertia.js v3 (React), Vite

**Banco de Dados:** SQLite (padrão), compatível com MySQL/PostgreSQL

**Serviços:** DomPDF, BaconQrCode, PhpSpreadsheet, Telegram Bot API, owen-it/laravel-auditing

## Requisitos

- PHP >= 8.3
- Composer
- Node.js >= 20
- SQLite (ou MySQL/PostgreSQL)

## Instalação

```bash
# 1. Clone o repositório
git clone <url-do-repositorio>
cd projeto-web

# 2. Configure o ambiente
cp .env.example .env

# 3. Execute o setup completo (instala dependências, gera chave, executa migrations, compila assets)
composer run setup
```

O comando `setup` executa automaticamente:
- `composer install`
- Geração da chave da aplicação
- Migrations do banco de dados
- `npm install && npm run build`

## Desenvolvimento

```bash
# Inicia os servidores em paralelo: Laravel, fila de jobs e Vite HMR
composer run dev
```

A aplicação estará disponível em `http://localhost:8000`.

## Configurações de Ambiente

Variáveis relevantes no `.env`:

```env
# Banco de dados (padrão: SQLite)
DB_CONNECTION=sqlite

# Filas (necessário para geração de relatórios)
QUEUE_CONNECTION=database

# Telegram (opcional, para envio de relatórios)
TELEGRAM_BOT_TOKEN=
TELEGRAM_CHAT_ID=
```

## Testes

```bash
# Rodar todos os testes
php artisan test --compact

# Rodar um arquivo específico
php artisan test --compact tests/Feature/ExampleTest.php

# Filtrar por nome
php artisan test --compact --filter=nomeDoTeste
```

## Build de Produção

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Para deploy em produção, recomendamos o [Laravel Cloud](https://cloud.laravel.com/).

## Estrutura Principal

```
app/
├── Http/Controllers/   # Controladores (+ Admin/)
├── Models/             # User, Contract, ContractPdf, GeneratedReport
├── Services/           # ReportExportService, ContractPdfService, TelegramService
├── Jobs/               # GenerateReportJob (assíncrono)
├── Policies/           # Autorização por recurso
└── Enums/              # ContractStatus, UserRole, ReportModule, ReportFormat

resources/js/
├── pages/              # Páginas React por módulo (contracts/, reports/, admin/, audits/, auth/)
└── components/         # Componentes reutilizáveis
```
