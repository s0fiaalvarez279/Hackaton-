<?php
session_name('transicontrol_session');
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['status' => 'error', 'message' => 'No autorizado']); exit; }

require_once __DIR__ . '/../app/models/SeguimientoModel.php';
header('Content-Type: application/json');

$data = SeguimientoModel::getAllWithDetails();
echo json_encode(['status' => 'success', 'data' => $data]);
?>