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
        if (isset($_GET['id'])) {
            $stmt = $db->prepare("SELECT * FROM proyectos WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $result = $stmt->fetch();
        } else {
            $result = $db->query("SELECT * FROM proyectos ORDER BY fecha_creacion DESC")->fetchAll();
        }
        echo json_encode($result);
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("INSERT INTO proyectos (titulo, descripcion, imagen_url, demo_url, github_url, tags) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$data['titulo'], $data['descripcion'], $data['imagen_url'], $data['demo_url'], $data['github_url'], $data['tags']])) {
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("UPDATE proyectos SET titulo=?, descripcion=?, imagen_url=?, demo_url=?, github_url=?, tags=? WHERE id=?");
        if ($stmt->execute([$data['titulo'], $data['descripcion'], $data['imagen_url'], $data['demo_url'], $data['github_url'], $data['tags'], $data['id']])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;
        
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $db->prepare("DELETE FROM proyectos WHERE id = ?");
            if ($stmt->execute([$id])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
        break;
}
?>