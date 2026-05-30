# TransiControl

## Nombre del Proyecto

TransiControl - Sistema de Gestión de Tránsito

## Descripción

TransiControl es un sistema web desarrollado en PHP puro que implementa el patrón MVC con arquitectura de Single Page Application (SPA). Proporciona una plataforma integral para la gestión de vehículos, seguimientos de infracciones y reportes en el ámbito del tránsito vehicular.

Incluye autenticación basada en sesiones, API REST para operaciones CRUD, y una interfaz de usuario responsiva construida con Bootstrap 5. El sistema permite gestionar usuarios, registros de vehículos, seguimiento de casos y generación de reportes con puntuación y comentarios.

## Objetivo

El objetivo principal de TransiControl es facilitar la administración eficiente de información de tránsito, permitiendo a las instituciones de tránsito:

- Registrar y mantener un historial de vehículos e infracciones
- Asignar y seguir casos de tránsito a usuarios específicos
- Generar reportes de seguimiento con evaluaciones
- Acceder a la información a través de una API REST estandarizada
- Gestionar usuarios con autenticación segura (incluyendo Google OAuth2)

Este proyecto fue desarrollado como solución técnica senior para la evaluación y gestión de incidencias de tránsito con interfaz moderna y experiencia de usuario optimizada.

## Tecnologías Usadas

### Backend
- **PHP 7.4+** - Lenguaje principal del servidor
- **MySQL 5.7+** - Base de datos relacional
- **PDO** - Conexión a base de datos con prepared statements
- **Bcrypt** - Hashing de contraseñas

### Frontend
- **Bootstrap 5** - Framework CSS (local)
- **Vanilla JavaScript** - Fetch API, async/await
- **SweetAlert2** - Notificaciones y diálogos
- **Font Awesome 6.4** - Iconografía
- **Leaflet** - Mapas interactivos

### Infraestructura
- **Apache** - Servidor web (XAMPP recomendado)
- **Composer** - Gestión de dependencias

## APIs y Datasets Consumidos

### APIs Externas
- **Google OAuth2 API** - Autenticación con cuenta Google (Login social)
  - Endpoint: `oauth2.googleapis.com/tokeninfo`
  - Verificación de tokens y obtención de datos de usuario

### Dataset Interno
- **Base de datos `bd_transito`** - Esquema relacional con las siguientes tablas:
  - `usuarios` - Registro de usuarios del sistema
  - `transito` - Vehículos e infracciones registradas
  - `seguimiento_transito` - Seguimiento de casos asignados
  - `reportes_transito` - Reportes y evaluaciones de seguimiento

### API REST Interna
- **Endpoints disponibles:**
  - `GET /api/transito.php` - Listar vehículos (paginación y búsqueda)
  - `POST /api/transito.php` - Crear vehículo
  - `PUT /api/transito.php?id=X` - Actualizar vehículo
  - `DELETE /api/transito.php?id=X` - Eliminar vehículo
  - `GET /api/seguimientos.php` - Obtener seguimientos
  - `GET /api/reportes.php` - Obtener reportes

## Requisitos

- PHP 7.4+
- MySQL 5.7+
- Servidor Apache (XAMPP recomendado)

## Instalación

1. Clonar o descargar el proyecto en `C:/xampp/htdocs/transicontrol`
2. Importar la base de datos desde `bd_transito.sql`
3. Configurar credenciales en `config/database.php`
4. Ejecutar `composer install` para instalar dependencias
5. Verificar `APP_URL` en `config/config.php`

## Credenciales de Prueba

| Email | Contraseña | Rol |
|-------|------------|-----|
| admin@transito.com | admin123 | Administrador |

## Estructura del Proyecto

```
├── app/
│   ├── controllers/     # Controladores MVC
│   ├── models/        # Modelos de datos
│   ├── views/         # Vistas PHP y SPA
│   └── middleware/    # AuthMiddleware
├── api/              # Endpoints REST
├── config/           # Configuración
├── public/           # Assets estáticos
└── vendor/           # Dependencias Composer
```

## Características

- ✅ Autenticación con sesiones y Google OAuth2
- ✅ Dashboard SPA sin recarga de página
- ✅ CRUD completo de vehículos vía API
- ✅ Seguimiento y reportes de infracciones
- ✅ Sidebar responsivo y offcanvas móvil
- ✅ Protección de rutas con middleware
- ✅ Paleta de colores traffic-themed
- ✅ Validación de formularios
- ✅ Estadísticas dinámicas

## Autor

Desarrollado para evaluación técnica senior.