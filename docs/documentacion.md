# ADMINOVA — Documentación del Proyecto

**Gestión de Alojamientos Turísticos**  
Eugenia Kostiukovskaia Essitachvili · 2º DAW · IES Pere Maria Orts i Bosch · 2024-25

---

## Índice

1. [Descripción del Proyecto](#1-descripción-del-proyecto)
2. [Arquitectura General](#2-arquitectura-general)
3. [Backend — Laravel 12](#3-backend--laravel-12)
4. [Frontend — Angular 18](#4-frontend--angular-18)
5. [Base de Datos — PostgreSQL](#5-base-de-datos--postgresql)
6. [Autenticación y Autorización](#6-autenticación-y-autorización)
7. [Funcionalidades Principales](#7-funcionalidades-principales)
8. [APIs Externas](#8-apis-externas)
9. [Despliegue con Docker](#9-despliegue-con-docker)
10. [Instalación en Local](#10-instalación-en-local)
11. [Control de Versiones](#11-control-de-versiones)
12. [Usuarios de Prueba](#12-usuarios-de-prueba)

---

## 1. Descripción del Proyecto

ADMINOVA es una aplicación web completa para la **gestión de alojamientos turísticos**. Permite a propietarios gestionar sus alojamientos, a huéspedes realizar reservas y a administradores supervisar todo el sistema desde un panel centralizado.

### Problema que resuelve

Los propietarios de alojamientos turísticos gestionan reservas, huéspedes y pagos con herramientas dispersas (email, Excel, WhatsApp) sin visión global ni automatización. ADMINOVA centraliza toda la gestión en una única plataforma.

### Objetivos

- Gestión completa de alojamientos con imágenes, precios y disponibilidad
- Sistema de reservas con verificación en tiempo real
- Autenticación segura con 4 roles diferenciados
- Generación automática de PDFs (confirmaciones y facturas)
- Envío de emails automáticos (confirmación y recordatorio)
- Integración con APIs externas (clima y mapas)
- Despliegue con Docker en dominio propio

---

## 2. Arquitectura General

ADMINOVA sigue una arquitectura **cliente-servidor** con separación completa entre frontend y backend:

```
┌─────────────────┐         ┌─────────────────┐         ┌─────────────────┐
│   Angular 18    │  HTTP   │   Laravel 12    │  SQL    │  PostgreSQL 15  │
│   (Frontend)    │ ──────► │   API REST      │ ──────► │  Base de datos  │
│   Puerto 4200   │  JSON   │   Puerto 8000   │         │   Puerto 5432   │
└─────────────────┘         └─────────────────┘         └─────────────────┘
```

La comunicación entre frontend y backend se realiza mediante una **API RESTful** con más de 80 endpoints, autenticada con **Laravel Sanctum** mediante tokens Bearer.

---

## 3. Backend — Laravel 12

### Tecnologías

| Tecnología | Versión | Uso |
|------------|---------|-----|
| **Laravel** | 12.x | Framework principal |
| **PHP** | 8.2 | Lenguaje de programación |
| **Laravel Sanctum** | Última | Autenticación con tokens API |
| **PostgreSQL** | 15+ | Base de datos relacional |
| **Spatie Media Library** | ^11.0 | Gestión de imágenes y multimedia |
| **mPDF** | ^8.2 | Generación de PDFs |
| **Guzzle HTTP** | ^7.0 | Cliente HTTP para APIs externas |
| **Laravel Mail** | Incluido | Envío de emails con Mailtrap |

### Estructura del Backend

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/    # 20+ controladores
│   │   ├── Requests/           # Form Requests (validación)
│   │   ├── Resources/          # API Resources (respuestas JSON)
│   │   └── Middleware/         # Middlewares personalizados
│   ├── Models/                 # 24 modelos Eloquent
│   ├── Policies/               # Autorización por roles
│   ├── Services/               # Lógica de negocio
│   ├── Jobs/                   # Tareas en cola
│   └── Mail/                   # Mailables
├── database/
│   ├── migrations/             # Migraciones de la BD
│   └── seeders/                # Datos de prueba
└── routes/
    ├── api.php                 # Rutas de la API (80+ endpoints)
    └── console.php             # Tareas programadas
```

### Características Técnicas

- **API Resources** para respuestas JSON estructuradas y consistentes
- **Form Requests** para validación de datos en el servidor
- **Policies** para autorización granular por roles (RBAC)
- **Eager Loading** para evitar el problema N+1 en consultas
- **SoftDeletes** en todos los modelos (eliminación lógica)
- **Relaciones polimórficas** en Media y Notifications
- **Custom Path Generator** para organizar archivos de Spatie Media Library

### Generación de PDFs

Se usa **mPDF** para generar documentos PDF directamente desde Laravel:

- **Confirmación de reserva** — disponible para descargar en cualquier momento
- **Factura** — generada tras el check-out, registrada en la base de datos

```
GET /api/bookings/{id}/download-confirmation
POST /api/bookings/{id}/generate-invoice
GET  /api/bookings/{id}/download-invoice
```

### Emails Automáticos

Se usa **Laravel Mail** con **Markdown Mailables** y **Mailtrap** como servidor SMTP:

- **BookingConfirmation** — se envía al crear una reserva
- **BookingReminder** — se envía 2 días antes del check-in (Job programado)

### Tareas Programadas (Scheduler)

```php
// routes/console.php
Schedule::command('geocode:addresses')->daily();

Schedule::call(function () {
    Booking::with(['guest', 'accommodation'])
        ->where('status', 'confirmed')
        ->whereDate('check_in', now()->addDays(2))
        ->each(fn($booking) => SendBookingReminder::dispatch($booking));
})->daily();
```

- **GeocodeAddresses** — geocodifica diariamente los alojamientos sin coordenadas GPS usando OpenStreetMap Nominatim
- **SendBookingReminder** — envía recordatorio de reserva 2 días antes del check-in

---

## 4. Frontend — Angular 18

### Tecnologías

| Tecnología | Versión | Uso |
|------------|---------|-----|
| **Angular** | ^18.2.0 | Framework SPA principal |
| **TypeScript** | ~5.5.0 | Lenguaje de programación |
| **Tailwind CSS** | ^3.4.19 | Framework CSS de utilidades |
| **PostCSS + Autoprefixer** | ^8.x | Procesador CSS |
| **Leaflet** | ^1.9.4 | Mapas interactivos |
| **Chart.js** | ^4.5.1 | Gráficas y estadísticas |
| **RxJS** | ~7.8.0 | Programación reactiva |
| **Angular Router** | ^18.2.0 | Navegación y Guards |

### Estructura del Frontend

```
frontend/src/app/
├── core/
│   ├── guards/             # AuthGuard, RoleGuard, ProfileCompleteGuard
│   ├── interceptors/       # AuthInterceptor (token Bearer)
│   ├── models/             # Interfaces TypeScript
│   └── services/           # Servicios HTTP
├── features/
│   ├── accommodations/     # Listado y detalle de alojamientos
│   ├── admin/              # Panel de administración
│   ├── auth/               # Login y registro
│   ├── bookings/           # Gestión de reservas
│   ├── contact/            # Página de contacto
│   ├── guest/              # Panel de huésped
│   ├── owner/              # Panel de propietario
│   └── staff/              # Panel de staff
└── shared/
    ├── components/         # Componentes reutilizables
    └── utils/              # Utilidades
```

### Características del Frontend

**Autenticación y sesión:**
- Token Bearer guardado en `localStorage`
- `AuthInterceptor` añade el token automáticamente a todas las peticiones
- Guards protegen las rutas según autenticación y rol

**Validación de formularios:**
- Validación local en Angular (doble capa con el backend)
- Expresiones regulares para email y teléfono en el registro
- Mensajes de error en tiempo real

**Operaciones con arrays:**
- `filter()` — filtrar reservas por estado
- `map()` — transformar respuestas de la API
- `find()` — buscar reserva para habilitar reseña
- `reduce()` — calcular media de valoraciones
- `slice()` — mostrar las primeras imágenes en la galería

**Optimización:**
- Skeleton loaders mientras cargan los datos
- Lazy loading de componentes con `loadComponent`
- Thumbnails antes de la imagen original

**Almacenamiento local:**
- `localStorage` — token, usuario y rol (persiste entre sesiones)
- `redirectAfterLogin` — guarda la URL destino tras login

### Protección de Rutas

```typescript
// Ejemplo de ruta protegida por rol
{
  path: 'admin/dashboard',
  loadComponent: () => import('./features/admin/dashboard/admin-dashboard.component'),
  canActivate: [AuthGuard, RoleGuard],
  data: { roles: ['admin'] }
}
```

- **AuthGuard** — redirige a login si no está autenticado
- **RoleGuard** — redirige si el rol no tiene acceso
- **ProfileCompleteGuard** — obliga al guest a completar su perfil antes de reservar

---

## 5. Base de Datos — PostgreSQL

### Características

| Característica | Descripción |
|----------------|-------------|
| **Tablas** | 24 tablas normalizadas |
| **Relaciones polimórficas** | Media, Notifications, Documents |
| **SoftDeletes** | Eliminación lógica en todas las tablas |
| **Foreign keys** | Integridad referencial con cascade/nullOnDelete |
| **Extensión unaccent** | Búsquedas sin tildes |

> **Requisito:** Ejecutar antes del primer despliegue:
> ```sql
> CREATE EXTENSION IF NOT EXISTS unaccent;
> ```

### Tablas Principales

| Tabla | Descripción |
|-------|-------------|
| `users` | Usuarios del sistema (todos los roles) |
| `accommodations` | Alojamientos con precios y configuración |
| `bookings` | Reservas con estados y totales |
| `payments` | Pagos con referencia única |
| `guests` | Perfil extendido de huéspedes |
| `owners` | Perfil extendido de propietarios |
| `media` | Archivos multimedia (Spatie) |
| `availability_calendars` | Disponibilidad por fecha |
| `reviews` | Reseñas de reservas completadas |
| `notifications` | Notificaciones del sistema |
| `loyalty_points` | Puntos de fidelidad |
| `cancellation_policies` | Políticas de cancelación |
| `cleaning_tasks` | Tareas de limpieza para staff |
| `messages` | Mensajería interna |
| `documents` | Documentos generados |

### Relaciones Principales

```
User ──── Guest (1:1)
User ──── Owner (1:1)
Owner ──── Accommodations (1:N)
Accommodation ──── Bookings (1:N)
Accommodation ──── AvailabilityCalendars (1:N)
Accommodation ──── Media (polimórfica)
Booking ──── Payment (1:1)
Booking ──── Review (1:1)
```

---

## 6. Autenticación y Autorización

### Autenticación con Sanctum

Al hacer login, el backend genera un **token personal** que el frontend guarda en `localStorage` y envía en cada petición mediante el header `Authorization: Bearer {token}`.

```
POST /api/login → { token, user }
POST /api/logout → invalida el token
GET  /api/user  → datos del usuario autenticado
```

### 4 Roles del Sistema

| Rol | Acceso |
|-----|--------|
| **admin** | Gestión total del sistema |
| **owner** | Solo lectura: visualiza sus propiedades, reservas recibidas e ingresos. La gestora administra sus alojamientos en su nombre |
| **guest** | Sus reservas, perfil, puntos y reseñas |
| **staff** | Tareas de limpieza asignadas |

### Autorización con Policies

Laravel Policies controlan qué puede hacer cada rol:

```php
// Ejemplo: solo el admin puede editar alojamientos
// El owner solo puede ver los suyos
public function update(User $user, Accommodation $accommodation): bool
{
    return $user->role === 'admin';
}

public function view(User $user, Accommodation $accommodation): bool
{
    return $user->role === 'admin' || 
           $accommodation->owner->user_id === $user->id;
}
```

---

## 7. Funcionalidades Principales

### Sistema de Reservas

Flujo completo:
1. **Búsqueda** — listado de alojamientos con filtros
2. **Detalle** — información completa con galería, mapa y clima
3. **Selección de fechas** — verificación de disponibilidad en tiempo real
4. **Checkout** — resumen con cálculo automático del precio
5. **Pago ficticio** — simulación del pago con métodos seleccionables
6. **Confirmación** — email automático + notificación en la plataforma

### Verificación de Disponibilidad

El sistema verifica disponibilidad considerando:
- Solapamiento directo de fechas
- Buffer de limpieza entre reservas (configurable por alojamiento)

```
POST /api/availability/check
{ accommodation_id, check_in, check_out } → { available: true/false }
```

### Pasarela de Pagos (Ficticia)

La pasarela simula el flujo completo de pago:
- El huésped selecciona método de pago
- Se crea un registro en `payments` con referencia única `PAY-XXXXXXXX`
- Se actualiza el estado de la reserva a `confirmed` y `payment_status` a `paid`
- Se genera una notificación automática

### Gestión de Imágenes

Se usa **Spatie Media Library** para:
- Subida de imágenes con thumbnails automáticos
- Organización en carpetas por modelo (`accommodations/{id}/`)
- Marca de imagen principal (`is_main` en `custom_properties`)
- Reordenación mediante drag & drop

### Puntos de Fidelidad

Los huéspedes acumulan puntos de bienvenida (300 puntos) al registrarse y pueden consultarlos en su panel.

---

## 8. APIs Externas

| Servicio | Uso | Tipo |
|----------|-----|------|
| **OpenWeatherMap** | Clima actual y pronóstico en detalle de alojamiento | REST API |
| **OpenStreetMap Nominatim** | Geocodificación de direcciones (dirección → coordenadas GPS) | REST API |
| **Leaflet** | Mapas interactivos en la página de detalle | Librería JS |
| **Mailtrap** | Servidor SMTP para envío de emails en desarrollo | SMTP |

---

## 9. Despliegue con Docker

### Arquitectura Docker

El proyecto usa **Docker Compose** para orquestar 3 servicios:

| Servicio | Imagen | Puerto |
|----------|--------|--------|
| `db` | postgres:15-alpine | 5432 |
| `backend` | php:8.2-fpm + nginx | 8000 (HTTPS) |
| `frontend` | node:20-alpine + nginx | 443 (HTTPS) |

### Comandos de Despliegue

```bash
# Arrancar todo (primera vez)
cd ~/Proyecto_Adminova
docker compose up --build -d

# Arrancar (siguientes veces)
docker compose up -d

# Parar
docker compose down

# Ver estado
docker ps

# Reconstruir un servicio
docker compose up --build backend -d
```

### Acceso

Una vez levantado, la aplicación es accesible en:
- **Frontend:** `https://adminova.es`
- **API:** `https://adminova.es:8000/api`

> El dominio `adminova.es` está configurado en `/etc/hosts` de la VM apuntando a `127.0.0.1`.

### Entrypoint del Backend

Al arrancar el contenedor del backend se ejecutan automáticamente:
1. Espera a que PostgreSQL esté listo
2. `php artisan config:cache`
3. `php artisan route:cache`
4. `php artisan migrate --force`
5. `php artisan storage:link`

---

## 10. Instalación en Local

### Requisitos

- PHP 8.2 + Composer
- Node.js 20+
- PostgreSQL 15
- Git

### Backend

```bash
cd backend
composer install
cp .env.example .env
# Configurar .env con las credenciales de la BD
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

### Frontend

```bash
cd frontend
npm install
ng serve
```

### Configuración del entorno

```env
# backend/.env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=adminova_db
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_usuario_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
```

### Variables de entorno del Frontend

```typescript
// frontend/src/environments/environment.ts (desarrollo)
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api'
};

// frontend/src/environments/environment.prod.ts (producción)
export const environment = {
  production: true,
  apiUrl: 'https://adminova.es:8000/api'
};
```

---

## 11. Control de Versiones

| Tecnología | Uso |
|------------|-----|
| **Git** | Control de versiones local |
| **GitHub** | Repositorio remoto |

Repositorio: `https://github.com/lilalini/Proyecto_Adminova`

Convención de commits:
- `feat:` — nueva funcionalidad
- `fix:` — corrección de errores
- `docs:` — documentación
- `refactor:` — refactorización

---

## 12. Usuarios de Prueba

| Rol | Email | Contraseña |
|-----|-------|-----------|
| Admin | admin@example.com | 123456 |
| Owner | owner@example.com | password |
| Staff | staff@example.com | password |

> Los usuarios se crean con los seeders: `php artisan db:seed`

---

## Conclusiones Técnicas

- **API REST** completa con más de 80 endpoints organizados con `apiResource`
- **Autenticación segura** mediante tokens Sanctum sin CSRF
- **Separación de responsabilidades** clara entre backend y frontend
- **Doble validación** — Form Requests en Laravel + validación local en Angular
- **Despliegue containerizado** con Docker Compose y HTTPS
- **Emails automáticos** con confirmación y recordatorio pre-estancia
- **Escalabilidad** gracias a la arquitectura modular y desacoplada
