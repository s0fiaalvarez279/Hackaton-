<?php
// config/config.php
define('BASE_URL', '/transicontrol');  // Ajusta según tu carpeta
define('APP_NAME', 'TransiControl');
define('DEBUG', true);

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();