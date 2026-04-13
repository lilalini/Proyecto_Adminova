# ADMINOVA - Documentación Tecnológica

## **Arquitectura General**

ADMINOVA es una aplicación web completa de gestión de alojamientos turísticos, desarrollada con **Laravel 12** en el backend y **Angular 18** en el frontend. La comunicación entre ambas capas se realiza mediante una API RESTful autenticada con **Laravel Sanctum**.

---

## **Backend (Laravel 12)**

| Tecnología | Versión | Uso |
|------------|---------|-----|
| **Laravel** | 12.x | Framework principal |
| **Laravel Sanctum** | Última | Autenticación con tokens API |
| **PostgreSQL** | 15+ | Base de datos relacional |
| **Spatie Media Library** | ^11.0 | Gestión de imágenes y archivos multimedia |
| **mPDF** | ^8.2 | Generación de PDF (facturas, contratos) |
| **Guzzle HTTP** | ^7.0 | Cliente HTTP para APIs externas |
| **OpenStreetMap Nominatim** | Servicio externo | Geocodificación (dirección → coordenadas) |

### **Características del Backend**
- 24 tablas normalizadas con relaciones polimórficas
- SoftDeletes en todas las tablas
- Políticas (Policies) para autorización por roles (admin, owner, guest, staff)
- Form Requests para validación de datos
- API Resources para respuestas JSON estructuradas

---

## **Frontend (Angular 18)**

| Tecnología | Versión | Uso |
|------------|---------|-----|
| **Angular** | ^18.2.0 | Framework principal |
| **Tailwind CSS** | ^3.4.19 | Framework CSS (utilidades) |
| **PostCSS** | ^8.5.8 | Procesador CSS (integrado) |
| **Autoprefixer** | ^10.4.27 | Prefijos CSS automáticos |
| **Leaflet / ngx-leaflet** | ^1.9.4 / ^21.0.0 | Mapas interactivos |
| **Chart.js** | ^4.5.1 | Gráficas y estadísticas |
| **Lucide Icons** | ^0.60.0 | Iconos vectoriales |
| **RxJS** | ~7.8.0 | Programación reactiva |
| **Angular Router** | ^18.2.0 | Navegación y protección de rutas |
| **LocalStorage** | Nativo | Almacenamiento local de sesión |

### **Configuración de Estilos**
```css
/* src/styles.css */
@tailwind base;
@tailwind components;
@tailwind utilities;
```
### **Configuración de Tailwind**

```javascript
// tailwind.config.js
module.exports = {
  content: ["./src/**/*.{html,ts}"],
  theme: { extend: {} },
  plugins: [],
}
```

## **Servicios Externos**

| Servicio | Uso | Documentación |
|----------|-----|---------------|
| **OpenStreetMap Nominatim** | Convertir dirección en coordenadas (geocodificación) | [nominatim.org](https://nominatim.org) |
| **OpenWeatherMap** | Clima actual y pronóstico | [openweathermap.org](https://openweathermap.org) |
| **Leaflet** | Visualización de mapas | [leafletjs.com](https://leafletjs.com) |

---

## **Base de Datos (PostgreSQL)**

| Característica | Descripción |
|----------------|-------------|
| **Tablas** | 24 tablas normalizadas |
| **Relaciones polimórficas** | Notificaciones, documentos, archivos multimedia |
| **SoftDeletes** | Eliminación lógica en todas las tablas |
| **Índices** | Optimización de consultas frecuentes |
| **Foreign keys** | Integridad referencial con cascade/nullOnDelete |

---

## **Seguridad y Autenticación**

| Tecnología | Uso |
|------------|-----|
| **Laravel Sanctum** | API tokens para autenticación |
| **Middleware auth:sanctum** | Protección de rutas |
| **Políticas (Policies)** | Autorización por roles (admin, owner, guest, staff) |
| **Form Requests** | Validación de datos de entrada |
| **CORS** | Configuración para comunicación con Angular |

---

## **Control de Versiones**

| Tecnología | Uso |
|------------|-----|
| **Git** | Control de versiones |
| **GitHub** | Repositorio remoto |

---

## **Resumen de Tecnologías**

| Capa | Tecnologías Principales |
|------|------------------------|
| **Backend** | Laravel, PostgreSQL, Sanctum, Spatie Media Library, mPDF |
| **Frontend** | Angular, Tailwind CSS, Leaflet, Chart.js, RxJS |
| **Servicios Externos** | OpenStreetMap Nominatim, OpenWeatherMap |
| **Infraestructura** | Git, GitHub |

---

##  **Conclusiones Técnicas**

- **API REST** completa con 24 endpoints principales
- **Autenticación segura** mediante tokens Sanctum
- **Separación de responsabilidades** clara entre backend y frontend
- **Escalabilidad** gracias a la arquitectura modular
- **Compatibilidad entre navegadores** asegurada con PostCSS/Autoprefixer