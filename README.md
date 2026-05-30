# TransiControl - Sistema de Gestión de Tránsito

## Descripción
Sistema web MVC con SPA, API REST, autenticación por sesiones y CRUD completo para la gestión de vehículos, seguimientos de infracciones y reportes. Desarrollado en PHP puro, MySQL, Bootstrap 5 y Vanilla JavaScript.

## Requisitos
- PHP 7.4+
- MySQL 5.7+
- Servidor Apache (XAMPP recomendado)

## Instalación
1. Descargar y descomprimir en `C:/xampp/htdocs/transicontrol`
2. Importar la base de datos desde `sql/bd_transito.sql` (phpMyAdmin o línea de comandos)
3. Configurar credenciales en `config/database.php` (usuario root, contraseña vacía por defecto)
4. Descargar Bootstrap 5 localmente en `public/assets/bootstrap/` desde https://getbootstrap.com
5. Asegurar que las rutas del proyecto coincidan con `transicontrol/` en el servidor

## Credenciales de prueba
- **Email:** admin@transito.com
- **Contraseña:** admin123

## Uso de la API
- `GET /api/transito.php` - Listar vehículos (con paginación y búsqueda)
- `POST /api/transito.php` - Crear vehículo
- `PUT /api/transito.php/{id}` - Actualizar vehículo
- `DELETE /api/transito.php/{id}` - Eliminar vehículo
- `GET /api/seguimientos.php` - Obtener seguimientos
- `GET /api/reportes.php` - Obtener valoraciones

## Estructura del Proyecto
- `app/` - Controladores, Modelos, Vistas, Middleware
- `api/` - Endpoints REST
- `public/` - Assets, CSS, JS, Bootstrap local
- `config/` - Configuración de base de datos y sistema

## Tecnologías
- PHP MVC
- MySQL
- Bootstrap 5 (local)
- SweetAlert2
- Vanilla JS (Fetch API, async/await)
- SPA sin recarga de página

## Características
✅ Login/Logout con sesiones  
✅ Dashboard con estadísticas dinámicas  
✅ CRUD de vehículos (modal, toast, SweetAlert2)  
✅ Listado de seguimientos y reportes  
✅ Sidebar responsivo + Offcanvas móvil  
✅ Protección de rutas vía middleware  
✅ API REST JSON  
✅ Paleta de colores inspirada en señalización vial  

## Autor
Desarrollado para evaluación técnica senior.