# TransiControl - Documentación Técnica

## 1. Arquitectura del Sistema

### 1.1 Patrón de Diseño
- **MVC (Modelo-Vista-Controlador)** implementado en PHP puro
- **SPA (Single Page Application)** con recargas asincrónicas via Fetch API
- **API REST** para operaciones CRUD

### 1.2 Estructura de Directorios
```
├── app/
│   ├── controllers/          # Controladores de la aplicación
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── DashboardController.php
│   │   ├── SeguimientoController.php
│   │   ├── TransitoController.php
│   │   └── ReporteController.php
│   ├── models/               # Modelos de datos
│   │   ├── UsuarioModel.php
│   │   ├── TransitoModel.php
│   │   ├── SeguimientoModel.php
│   │   └── ReporteModel.php
│   ├── views/                # Vistas PHP
│   │   ├── layouts/
│   │   │   └── spa_base.php
│   │   ├── home/
│   │   │   └── index.php
│   │   └── auth/
│   │       ├── login.php
│   │       └── google.php
│   └── middleware/
│       └── AuthMiddleware.php
├── api/                      # Endpoints REST
│   ├── transito.php
│   ├── seguimientos.php
│   └── reportes.php
├── config/
│   ├── config.php           # Configuración general
│   └── database.php         # Singleton PDO
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── login.css
│   │   │   └── home.css
│   │   ├── js/
│   │   │   ├── login.js
│   │   │   ├── home.js
│   │   │   └── dashboard.js
│   │   └── bootstrap/       # Bootstrap 5 local
│   └── images/
├── .htaccess
├── index.php                 # Front controller
└── bd_transito.sql          # Script de base de datos
```

## 2. Modelo de Datos

### 2.1 Esquema de Base de Datos (MySQL)

#### Tabla: `usuarios`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_usuario` | INT (PK, AI) | Identificador único |
| `nombre` | VARCHAR(100) | Nombre del usuario |
| `email` | VARCHAR(100) UNIQUE | Email para login |
| `clave` | VARCHAR(255) | Password hash (bcrypt) |
| `fecha_register` | DATE | Fecha registro |

#### Tabla: `transito`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_transito` | INT (PK, AI) | Identificador del vehículo |
| `placa` | VARCHAR(20) | Placa del vehículo |
| `tipo_vehiculo` | VARCHAR(50) | Tipo (Automóvil, Moto, Camión) |
| `fecha_registro` | DATE | Fecha del registro |

#### Tabla: `seguimiento_transito`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_seguimiento` | INT (PK, AI) | Identificador del seguimiento |
| `id_usuario` | INT (FK) | Referencia a usuarios |
| `id_transito` | INT (FK) | Referencia a transito |
| `estado` | VARCHAR(20) | pendiente/en proceso/finalizado |
| `fecha_inicio` | DATE | Fecha de inicio |

#### Tabla: `reportes_transito`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_reporte` | INT (PK, AI) | Identificador del reporte |
| `id_seguimiento` | INT (FK) | Referencia única a seguimiento |
| `puntuacion` | INT (1-10) | Calificación del caso |
| `comentario` | TEXT | Comentarios del reporte |
| `fecha_reporte` | DATE | Fecha del reporte |

### 2.2 Relaciones
- `usuarios` 1:N `seguimiento_transito`
- `transito` 1:N `seguimiento_transito`
- `seguimiento_transito` 1:1 `reportes_transito`

## 3. Routing y Controladores

### 3.1 Front Controller (`index.php`)

| Ruta | Método | Handler |
|------|--------|---------|
| `/` | GET | HomeController::index() |
| `/login` | GET | app/views/auth/login.php |
| `/dashboard` | GET | DashboardController::index() |
| `/logout` | GET | AuthController::logout() |
| `/auth/login` | POST | AuthController::loginJSON() |
| `/auth/register` | POST | AuthController::registerJSON() |

### 3.2 Endpoints API REST

| Endpoint | Método | Descripción | Auth |
|----------|--------|-------------|------|
| `/api/transito.php` | GET | Listar vehículos (paginación/busqueda) | ✓ |
| `/api/transito.php?id=X` | GET | Obtener vehículo por ID | ✓ |
| `/api/transito.php` | POST | Crear vehículo | ✓ |
| `/api/transito.php?id=X` | PUT | Actualizar vehículo | ✓ |
| `/api/transito.php?id=X` | DELETE | Eliminar vehículo | ✓ |
| `/api/seguimientos.php` | GET | Listar seguimientos con detalles | ✓ |
| `/api/reportes.php` | GET | Listar reportes con detalles | ✓ |

## 4. Autenticación y Seguridad

### 4.1 Middleware (`AuthMiddleware.php`)
```php
session_name('transicontrol_session');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /transicontrol/');
    exit;
}
```

### 4.2 Credenciales de Sesión
- `user_id`: ID del usuario
- `user_name`: Nombre completo
- `user_email`: Email del usuario
- `logged_in`: Bandera booleana

### 4.3 Autenticación Google OAuth2
- Endpoint: `/auth/google` (POST)
- Token verification via `oauth2.googleapis.com/tokeninfo`
- Creación automática de usuarios si no existen

## 5. Dependencias

### 5.1 Backend
- PHP 7.4+
- MySQL 5.7+
- PDO con prepared statements
- Bcrypt para hashing de passwords

### 5.2 Frontend
- Bootstrap 5 (local)
- Font Awesome 6.4
- SweetAlert2
- Google Sign-In API (GSI)
- Swiper.js (carousel)

## 6. Funcionalidades Principales

### 6.1 Autenticación
- Login/logout con sesiones
- Registro de usuarios
- Login con Google OAuth2
- Validación de contraseña (8+ caracteres, mayúscula, número, especial)
- Recuperación de contraseña (simulada)

### 6.2 Dashboard SPA
- Sidebar responsivo (desktop/mobile)
- Offcanvas para móviles
- Navegación sin recarga vía JavaScript
- Estadísticas dinámicas

### 6.3 CRUD Vehículos (API)
- Listado con paginación
- Búsqueda por placa/tipo
- Creación/edición/eliminación vía modals

### 6.4 Home (Landing Page)
- Mapa Leaflet fullscreen
- Estadísticas en tiempo real (simuladas)
- Panel lateral con vehículos
- Chat IA (simulado)
- Simulador de lluvia/intensidad

## 7. Flujos de Trabajo

### 7.1 Login Flow
```
1. Usuario ingresa credenciales en /login
2. POST a /auth/login con email/password
3. UsuarioModel::buscarPorEmail() valida credenciales
4. Si válido: sesión creada + redirect a /dashboard
5. Si inválido: error 401 → mensaje toast
```

### 7.2 Registro Flow
```
1. Usuario completa formulario de registro
2. POST a /auth/register
3. UsuarioModel::crearUsuario() inserta registro
4. Auto-login + redirect a /dashboard
```

### 7.3 CRUD Vehículos Flow
```
1. Frontend llama fetch() a /api/transito.php
2. AuthMiddleware verifica sesión
3. TransitoModel ejecuta operación (getAll/create/update/delete)
4. Respuesta JSON con status/data
5. UI actualiza sin recargar página
```

## 8. Configuración

### 8.1 `config/config.php`
```php
date_default_timezone_set('America/Bogota');
define('APP_NAME', 'TransiControl');
define('APP_URL', 'http://localhost/transicontrol');
define('SESSION_NAME', 'transicontrol_session');
```

### 8.2 `config/database.php` (Singleton)
```php
class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $dbname = 'bd_transito';
    public static function getConnection(); // Retorna PDO
}
```

## 9. API Response Format

### 8.6.1 Éxito
```json
{
  "status": "success",
  "data": [...],
  "total": 100,
  "page": 1,
  "limit": 10
}
```

### 8.6.2 Error
```json
{
  "status": "error",
  "message": "Descripción del error"
}
```

## 10. Estilos y Temas

### 10.1 Paleta de Colores (Traffic-themed)
| Variable | Valor | Uso |
|----------|-------|-----|
| `--primary` | `#0F4C81` | Azul principal (semáforo) |
| `--accent` | `#F9A825` | Amarillo/ámbar (alerta) |
| `--ok` | `#10b981` | Verde (éxito/normal) |
| `--warn` | `#f59e0b` | Naranja (advertencia) |
| `--danger` | `#ef4444` | Rojo (crítico) |

## 11. Instalación y Despliegue

### 11.1 Requisitos
- PHP 7.4+ con extension PDO MySQL
- MySQL 5.7+
- Servidor Apache (XAMPP recomendado)

### 11.2 Pasos
1. Clonar proyecto en `htdocs/transicontrol`
2. Importar `bd_transito.sql` via phpMyAdmin
3. Configurar credenciales en `config/database.php`
4. Verificar `APP_URL` en `config/config.php`
5. Configurar Google Client ID en `login.php` (opcional)

## 12. Usuarios de Prueba
| Email | Contraseña | Rol |
|-------|------------|-----|
| admin@transito.com | admin123 | Administrador |