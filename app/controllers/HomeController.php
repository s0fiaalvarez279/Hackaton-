<?php
// app/controllers/HomeController.php
class HomeController {
    public function index() {
        // Cargar la vista de inicio (landing page pública)
        require_once __DIR__ . '/../views/home/index.php';
    }
}