# Estudo Claude — Sistema de Gestão de Contratos 

Sistema interno para gestão de contratos, relatórios e auditoria, desenvolvido com Laravel 13 + Inertia.js v3 + React 19.

## Stack

| Camada | Tecnologia |
|---|---|
| Backend | PHP 8.5 / Laravel 13 |
| Frontend | React 19 / Inertia.js v3 |
| Estilização | Tailwind CSS v4 |
| Autenticação | Laravel Fortify |
| Rotas tipadas | Laravel Wayfinder |
| Testes | PHPUnit 12 |
| IA | Google Gemini API |

## Funcionalidades

### Contratos
- CRUD completo com status (Pendente, Ativo, Concluído, Cancelado)
- Geração de PDFs com QR code de validação
- Upload de arquivos anexados (PDF, DOC, XLS, imagens — máx. 20MB)
- Análise com IA: resumo executivo via Google Gemini

### Relatórios
- Geração assíncrona (via Jobs) em Excel, PDF e CSV
- Filtros por período, usuário, status
- Envio via Telegram
- Análise com IA para relatórios em PDF

### Log de Auditoria
- Registro de todas as ações (criação, edição, exclusão)
- Visualização com diff inline de valores alterados
- Filtros por evento, módulo, usuário, IP e data

### Administração
- Gestão de usuários com roles (admin / usuário)
- Autenticação com 2FA (TOTP + QR code + recovery codes)

## Requisitos

- PHP 8.5+
- Node.js 20+
- Composer

## Instalação

```bash
# Dependências
composer install
npm install

# Ambiente
cp .env.example .env
php artisan key:generate

# Banco de dados
php artisan migrate --seed

# Build frontend
npm run build
```

## Variáveis de ambiente obrigatórias

```env
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite          # ou mysql/pgsql

GEMINI_API_KEY=               # Google AI Studio — aistudio.google.com/apikey
TELEGRAM_BOT_TOKEN=           # opcional — envio de relatórios via Telegram
TELEGRAM_CHAT_ID=             # opcional
```

## Desenvolvimento

```bash
composer run dev   # inicia Laravel + Vite juntos
```

## Testes

```bash
php artisan test
```

## Segurança

- CSRF em todas as rotas web
- Autorização via Policies em todos os recursos
- Rate limiting nas rotas de análise IA (20 req/hora por usuário)
- Arquivos armazenados no disco local (fora do public)
- API key nunca exposta ao cliente
