<?php
// app/models/User.php
require_once __DIR__ . '/../../config/database.php';

class User {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Registrar un nuevo usuario
     * @param string $username
     * @param string $email
     * @param string $password
     * @return array ['success' => bool, 'message' => string]
     */
    public function register($username, $email, $password) {
        // Validar que no exista el email o username
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'El email o nombre de usuario ya está registrado.'];
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed])) {
            return ['success' => true, 'message' => 'Registro exitoso. Ya puedes iniciar sesión.'];
        }
        return ['success' => false, 'message' => 'Error al registrar el usuario.'];
    }

    /**
     * Iniciar sesión
     * @param string $email
     * @param string $password
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // No guardar contraseña en sesión
            return ['success' => true, 'message' => 'Inicio de sesión exitoso.', 'user' => $user];
        }
        return ['success' => false, 'message' => 'Email o contraseña incorrectos.', 'user' => null];
    }
}