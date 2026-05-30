<?php
session_name('transicontrol_session');
session_start();
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['status' => 'error', 'message' => 'No autorizado']); exit; }

require_once __DIR__ . '/../app/models/TransitoModel.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch($method) {
    case 'GET':
        if ($id) {
            $data = TransitoModel::find($id);
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $search = $_GET['search'] ?? '';
            $result = TransitoModel::getAll($page, $limit, $search);
            echo json_encode(['status' => 'success', 'data' => $result['data'], 'total' => $result['total'], 'page' => $result['page'], 'limit' => $result['limit']]);
        }
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $success = TransitoModel::create($input);
        echo json_encode(['status' => $success ? 'success' : 'error', 'message' => $success ? 'Creado exitosamente' : 'Error al crear']);
        break;
    case 'PUT':
        if ($id) {
            $input = json_decode(file_get_contents('php://input'), true);
            $success = TransitoModel::update($id, $input);
            echo json_encode(['status' => $success ? 'success' : 'error', 'message' => $success ? 'Actualizado correctamente' : 'Error al actualizar']);
        }
        break;
    case 'DELETE':
        if ($id) {
            $success = TransitoModel::delete($id);
            echo json_encode(['status' => $success ? 'success' : 'error', 'message' => $success ? 'Eliminado correctamente' : 'Error al eliminar']);
        }
        break;
}
?>