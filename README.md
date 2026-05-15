# ADMINOVA — Gestión de Alojamientos Turísticos

Aplicación web completa para la gestión de alojamientos turísticos, desarrollada con Laravel 12 en el backend y Angular 18 en el frontend.

## Tecnologías

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 12, PHP 8.2, Sanctum |
| Frontend | Angular 18, Tailwind CSS |
| Base de datos | PostgreSQL 15 |
| Multimedia | Spatie Media Library |
| PDFs | mPDF |
| Infraestructura | Docker, Docker Compose |

## Requisitos previos

- PHP 8.2 + Composer
- Node.js 20+
- PostgreSQL 15 con extensión `unaccent`

## Instalación local

### 1. Base de datos
```sql
CREATE EXTENSION IF NOT EXISTS unaccent;
```

### 2. Backend
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

### 3. Frontend
```bash
cd frontend
npm install
ng serve
```

## Despliegue con Docker
```bash
docker compose up --build
```
- Frontend: http://localhost:4200
- Backend: http://localhost:8000
- API: http://localhost:8000/api

## Usuarios de prueba

| Rol | Email | Contraseña |
|-----|-------|-----------|
| Admin | admin@example.com | 123456 |
| Owner | owner@example.com | password |
| Staff | staff@example.com | password |

## Funcionalidades principales

- Autenticación por tokens (Sanctum)
- 4 roles: admin, owner, guest, staff
- Gestión completa de alojamientos con imágenes
- Sistema de reservas con disponibilidad y buffer de limpieza
- Generación de PDFs (confirmaciones y facturas)
- Email de confirmación de reserva
- Pasarela de pagos simulada
- Tareas programadas (geocodificación automática)
- Mapas interactivos con Leaflet
- Clima en tiempo real con OpenWeatherMap
