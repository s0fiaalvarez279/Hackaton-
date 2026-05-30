<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TransiControl - Gestión de Tránsito</title>
    <!-- Bootstrap 5 Local -->
    <link href="/transicontrol/public/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/transicontrol/public/css/style.css">
    <link rel="stylesheet" href="/transicontrol/public/css/theme.css">
    <!-- SweetAlert2 Local (descargar y colocar en public/assets/) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Navbar Móvil -->
    <nav class="navbar navbar-dark fixed-top d-lg-none" style="background-color: #FFC107;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="bi bi-signpost-2"></i> TransiControl</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Offcanvas Móvil -->
    <div class="offcanvas offcanvas-start offcanvas-dark w-75 d-lg-none" tabindex="-1" id="offcanvasMenu">
        <div class="offcanvas-header bg-dark text-white">
            <h5 class="offcanvas-title"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body bg-secondary">
            <ul class="nav nav-pills flex-column" id="mobileMenu">
                <li class="nav-item"><a href="#" data-section="dashboard" class="nav-link text-white"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a href="#" data-section="seguimientos" class="nav-link text-white"><i class="bi bi-list-check"></i> Mis Entidades</a></li>
                <li class="nav-item"><a href="#" data-section="crud" class="nav-link text-white"><i class="bi bi-database"></i> Biblioteca CRUD</a></li>
                <li class="nav-item"><a href="#" data-section="reportes" class="nav-link text-white"><i class="bi bi-star"></i> Valoraciones</a></li>
                <li class="nav-item"><a href="/transicontrol/logout" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>

    <!-- Sidebar Escritorio -->
    <div class="sidebar d-none d-lg-block">
        <div class="sidebar-header">
            <h3><i class="bi bi-ev-station"></i> TransiControl</h3>
            <hr class="border-warning">
            <div class="user-info">
                <i class="bi bi-person-badge fs-3"></i>
                <p><strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong><br><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
            </div>
        </div>
        <ul class="nav flex-column" id="desktopMenu">
            <li class="nav-item"><a href="#" data-section="dashboard" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li class="nav-item"><a href="#" data-section="seguimientos" class="nav-link"><i class="bi bi-list-check"></i> Mis Entidades</a></li>
            <li class="nav-item"><a href="#" data-section="crud" class="nav-link"><i class="bi bi-database"></i> Biblioteca CRUD</a></li>
            <li class="nav-item"><a href="#" data-section="reportes" class="nav-link"><i class="bi bi-star"></i> Valoraciones</a></li>
            <li class="nav-item mt-5"><a href="/transicontrol/logout" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Contenido Dinámico SPA -->
    <div class="main-content">
        <div id="spinner" class="spinner-overlay d-none">
            <div class="spinner-border text-warning" role="status"></div>
        </div>
        <div id="dynamicContent" class="container-fluid p-4">
            <!-- Aquí se cargará el contenido vía JS -->
        </div>
    </div>

    <!-- Toasts -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3"></div>

    <!-- Scripts -->
    <script src="/transicontrol/public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/transicontrol/public/js/api.js"></script>
    <script src="/transicontrol/public/js/dashboard.js"></script>
    <script src="/transicontrol/public/js/transito.js"></script>
    <script src="/transicontrol/public/js/app.js"></script>
</body>
</html>