# Connex

Resource & Project Management System for integrator-contractors in the Israeli construction industry.

## Tech Stack

- **Backend**: Laravel 13 + Filament v5 (admin panel)
- **Frontend**: Nuxt 4 + Vuexy + Vuetify
- **Database**: PostgreSQL 16
- **Cache/Queue/Sessions**: Redis 7
- **File Storage**: S3-compatible

## Quick Start

### Prerequisites

- PHP 8.3+
- Node.js 22+
- Docker & Docker Compose

### 1. Start infrastructure

```bash
make up
```

### 2. Set up backend

```bash
cd backend
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### 3. Set up frontend

```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

### Common Commands

```bash
make fresh          # Reset DB and re-seed
make seed           # Run seeders
make dev-backend    # Start Laravel dev server
make dev-frontend   # Start Nuxt dev server
make dev            # Start both servers
```

## Project Structure

```
connex/
├── backend/     # Laravel 13 application
├── frontend/    # Vuexy Nuxt 4 application
└── docker-compose.yml
```
