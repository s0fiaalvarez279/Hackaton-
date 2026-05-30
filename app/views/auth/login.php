<?php
// login.php
session_start();
// Definir APP_URL (ajústala según tu entorno)
if (!defined('APP_URL')) {
    define('APP_URL', 'http://localhost:8000'); // Cambia por tu dominio
}
// Redirigir si ya está logueado
if (isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/dashboard');
    exit();
}

// --- CONFIGURACIÓN DE GOOGLE ---
// Colocar el Client ID real obtenido de Google Cloud Console
$google_client_id = 'TU_CLIENT_ID_DE_GOOGLE.apps.googleusercontent.com'; // ¡REEMPLAZA!

// Si no está configurado, se deshabilitará el botón
$google_configured = ($google_client_id !== 'TU_CLIENT_ID_DE_GOOGLE.apps.googleusercontent.com');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TransiControl · Acceso seguro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/assets/css/login.css">
    <script>
        const APP_URL = "<?= rtrim(APP_URL, '/') ?>";
        const GOOGLE_CLIENT_ID = "<?= htmlspecialchars($google_client_id) ?>";
        const GOOGLE_CONFIGURED = <?= $google_configured ? 'true' : 'false' ?>;
    </script>
</head>
<body>

<header class="login-header">
    <div class="header-container">
        <a href="<?= APP_URL ?>/" class="logo-link">
            <i class="fas fa-traffic-light"></i>
<<<<<<< HEAD
            <span>Transi<span>Control</span></span>
=======
<<<<<<< HEAD
            <span>Transi<span>Control</span></span> 
=======
            <span>Transi<span>Control</span></span>
>>>>>>> de973c3c64ca69fbfc8cc06d37143c6990d9aafb
>>>>>>> 6234cab8e71b79bb5937e534df305a343dee8dad
        </a>
        <a href="<?= APP_URL ?>/" class="back-link">
            <i class="fas fa-arrow-left"></i> Regresar
        </a>
    </div>
</header>

<main class="login-main">
    <div class="login-grid">
        <div class="login-brand">
            <div class="brand-content">
                <h2>Gestión inteligente<br>de movilidad urbana</h2>
                <div class="feature-list">
                    <div><i class="fas fa-check-circle"></i> Control centralizado de infracciones</div>
                    <div><i class="fas fa-check-circle"></i> Seguimiento en tiempo real</div>
                    <div><i class="fas fa-check-circle"></i> Reportes automatizados</div>
                    <div><i class="fas fa-check-circle"></i> Base de datos vehicular unificada</div>
                </div>
                <div class="trust-badge">
                    <i class="fas fa-shield-alt"></i> Datos seguros · Plataforma validada
                </div>
            </div>
        </div>

        <div class="login-form-container">
            <div class="form-card">
                <div class="form-tabs">
                    <button class="tab-btn active" data-tab="login">Iniciar sesión</button>
                    <button class="tab-btn" data-tab="register">Crear cuenta</button>
                </div>

                <div id="messageBox" class="msg-box" style="display: none;"></div>

                <!-- LOGIN -->
                <div id="loginForm" class="form-panel active">
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="loginEmail" placeholder="Correo electrónico" autocomplete="email">
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="loginPassword" placeholder="Contraseña">
                        <button type="button" class="toggle-pwd" onclick="togglePassword('loginPassword', this)">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    <div class="row-flex">
                        <label class="checkbox"><input type="checkbox" id="rememberLogin"> Recordarme</label>
                        <a href="#" id="forgotPassword" class="forgot-link">¿Olvidaste tu contraseña?</a>
                    </div>
                    <button class="btn-primary" id="loginButton" onclick="handleEmailLogin()">
                        <span>Acceder al sistema</span>
                        <div class="spinner" style="display:none"></div>
                    </button>

                    <!-- Separador -->
                    <div class="divider">
                        <span>O continúa con</span>
                    </div>

                    <!-- Botón de Google -->
                    <button type="button" class="btn-google-custom" id="googleSignInBtn" <?= !$google_configured ? 'disabled' : '' ?>>
                        <i class="fab fa-google"></i> 
                        <?= $google_configured ? 'Continuar con Google' : 'Google no configurado' ?>
                    </button>

                    <button type="button" class="btn-demo" onclick="demoLogin()">
                        <i class="fas fa-flask"></i> Modo demostración
                    </button>
                </div>

                <!-- REGISTRO -->
                <div id="registerForm" class="form-panel">
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="registerName" placeholder="Nombre completo">
                    </div>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="registerEmail" placeholder="Correo electrónico">
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="registerPassword" placeholder="Contraseña">
                        <button type="button" class="toggle-pwd" onclick="togglePassword('registerPassword', this)">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar"></div><div class="strength-bar"></div>
                        <div class="strength-bar"></div><div class="strength-bar"></div>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirmPassword" placeholder="Confirmar contraseña">
                        <button type="button" class="toggle-pwd" onclick="togglePassword('confirmPassword', this)">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    <div id="passwordMatch" class="match-hint"></div>
                    <label class="checkbox"><input type="checkbox" id="terms"> Acepto <a href="#">términos y condiciones</a></label>
                    <button class="btn-primary" id="registerButton" onclick="handleEmailRegister()">
                        <span>Registrarme</span>
                        <div class="spinner" style="display:none"></div>
                    </button>
                </div>

                <!-- RECUPERACIÓN -->
                <div id="forgotPasswordForm" class="form-panel">
                    <p class="info-text">Ingresa tu correo y te enviaremos instrucciones para restablecer tu contraseña.</p>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="forgotEmail" placeholder="Correo electrónico">
                    </div>
                    <button class="btn-primary" id="sendResetLink" onclick="handleForgotPassword()">
                        <span>Enviar enlace</span>
                        <div class="spinner" style="display:none"></div>
                    </button>
                    <a href="#" id="backToLogin" class="back-link">← Volver al inicio de sesión</a>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="login-footer">
    <div class="footer-content">
        <div class="footer-logo">
            <i class="fas fa-traffic-light"></i>
            <div class="footer-logo-text">TransiControl</div>
            <p>Sistema integral de gestión de tránsito urbano</p>
        </div>
        <div class="footer-section">
            <h3>Contacto</h3>
            <p>+57 300 123 4567</p>
            <p>info@transicontrol.com</p>
            <p>Bogotá, Colombia</p>
        </div>
        <div class="footer-section">
            <h3>Síguenos</h3>
            <div class="social-links">
                <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="#" target="_blank"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 TransiControl. Todos los derechos reservados.</p>
    </div>
</footer>

<script src="https://accounts.google.com/gsi/client" async defer></script>
<script src="<?= APP_URL ?>/public/assets/js/login.js"></script>
</body>
</html>