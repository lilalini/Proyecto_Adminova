# ADMINOVA

Proyecto de gestión de alojamientos.

## Tecnologías
- Backend: Laravel
- Frontend: Angular (próximamente)
- Base de datos: PostgreSQL

## Estructura
- `/backend` - API Laravel (22/24 tablas)
- `/frontend` - Cliente Angular (próximamente)

## Instalación

Backend:

composer install  
cp .env.example .env  
php artisan key:generate  
php artisan migrate

## Producción
El backend puede desplegarse en cualquier hosting compatible con PHP 8.2 + PostgreSQL.

añadir: 
1. PostgreSQL con extensión unaccent: CREATE EXTENSION IF NOT EXISTS unaccent;
2. En producción: quitar ->withOptions(['verify' => false]) de GeocodingService
3. Librería mPDF instalada: composer require mpdf/mpdf