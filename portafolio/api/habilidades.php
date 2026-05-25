<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Obtener todas las habilidades o una específica
        if (isset($_GET['id'])) {
            $stmt = $db->prepare("SELECT * FROM habilidades WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $result = $stmt->fetch();
        } else {
            $result = $db->query("SELECT * FROM habilidades ORDER BY orden")->fetchAll();
        }
        echo json_encode($result);
        break;
        
    case 'POST':
        // Crear nueva habilidad
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("INSERT INTO habilidades (nombre, icono, color, orden) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$data['nombre'], $data['icono'], $data['color'], $data['orden']])) {
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al crear']);
        }
        break;
        
    case 'PUT':
        // Actualizar habilidad
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("UPDATE habilidades SET nombre=?, icono=?, color=?, orden=? WHERE id=?");
        if ($stmt->execute([$data['nombre'], $data['icono'], $data['color'], $data['orden'], $data['id']])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;
        
    case 'DELETE':
        // Eliminar habilidad
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $db->prepare("DELETE FROM habilidades WHERE id = ?");
            if ($stmt->execute([$id])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
        break;
}
?>
