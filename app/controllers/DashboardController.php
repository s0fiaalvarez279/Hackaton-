<?php
// app/controllers/DashboardController.php
class DashboardController {
    public function index() {
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Cargar la vista del dashboard
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}