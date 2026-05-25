<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar datos
    if (!isset($data['nombre']) || !isset($data['email']) || !isset($data['asunto']) || !isset($data['mensaje'])) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos requeridos']);
        exit;
    }
    
    $nombre = filter_var($data['nombre'], FILTER_SANITIZE_STRING);
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $asunto = filter_var($data['asunto'], FILTER_SANITIZE_STRING);
    $mensaje = filter_var($data['mensaje'], FILTER_SANITIZE_STRING);
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Email inválido']);
        exit;
    }
    
    $stmt = $db->prepare("INSERT INTO contactos (nombre, email, asunto, mensaje) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$nombre, $email, $asunto, $mensaje])) {
        echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al enviar el mensaje']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
?>