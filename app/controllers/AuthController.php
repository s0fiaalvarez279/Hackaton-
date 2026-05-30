<?php
// /auth/google
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id_token = $input['id_token'] ?? '';

if (empty($id_token)) {
    echo json_encode(['success' => false, 'message' => 'Token no proporcionado']);
    exit;
}

// Verificar el token con Google
$google_api_url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $id_token;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $google_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['success' => false, 'message' => 'Token de Google inválido o expirado']);
    exit;
}

$user_data = json_decode($response, true);

// Verificar que el audience (client_id) coincida con el tuyo
$your_client_id = 'TU_CLIENT_ID_DE_GOOGLE.apps.googleusercontent.com';
if ($user_data['aud'] !== $your_client_id) {
    echo json_encode(['success' => false, 'message' => 'Audiencia incorrecta']);
    exit;
}

$email = $user_data['email'];
$nombre = $user_data['name'] ?? explode('@', $email)[0];
$google_id = $user_data['sub'];

// Aquí tu lógica de base de datos: buscar o crear usuario
session_start();
require_once '../config/database.php'; // Ajusta la ruta

$db = getConnection();

// Verificar si ya existe un usuario con ese email
$stmt = $db->prepare("SELECT id, nombre, email FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    // Registrar nuevo usuario con Google
    $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, google_id, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$nombre, $email, $google_id]);
    $user_id = $db->lastInsertId();
    $user_name = $nombre;
} else {
    $user_id = $usuario['id'];
    $user_name = $usuario['nombre'];
    // Opcional: actualizar google_id si no lo tenía
    if (empty($usuario['google_id'])) {
        $stmt = $db->prepare("UPDATE usuarios SET google_id = ? WHERE id = ?");
        $stmt->execute([$google_id, $user_id]);
    }
}

// Iniciar sesión (crear variables de sesión)
$_SESSION['user_id'] = $user_id;
$_SESSION['user_name'] = $user_name;
$_SESSION['user_email'] = $email;
$_SESSION['logged_in'] = true;

echo json_encode([
    'success' => true,
    'message' => 'Inicio de sesión con Google exitoso',
    'redirect' => APP_URL . '/dashboard'
]);
exit;