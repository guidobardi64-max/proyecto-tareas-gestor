<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$db = getDB();
$mensaje = '';

// Procesar acciones CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'crear') {
        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
        $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
        $imagen_url = filter_input(INPUT_POST, 'imagen_url', FILTER_SANITIZE_URL);
        $demo_url = filter_input(INPUT_POST, 'demo_url', FILTER_SANITIZE_URL);
        $github_url = filter_input(INPUT_POST, 'github_url', FILTER_SANITIZE_URL);
        $tags = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
        
        $stmt = $db->prepare("INSERT INTO proyectos (titulo, descripcion, imagen_url, demo_url, github_url, tags) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$titulo, $descripcion, $imagen_url, $demo_url, $github_url, $tags])) {
            $mensaje = '<div class="alert alert-success">Proyecto creado exitosamente</div>';
        }
    } 
    elseif ($action === 'editar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
        $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
        $imagen_url = filter_input(INPUT_POST, 'imagen_url', FILTER_SANITIZE_URL);
        $demo_url = filter_input(INPUT_POST, 'demo_url', FILTER_SANITIZE_URL);
        $github_url = filter_input(INPUT_POST, 'github_url', FILTER_SANITIZE_URL);
        $tags = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING);
        
        $stmt = $db->prepare("UPDATE proyectos SET titulo=?, descripcion=?, imagen_url=?, demo_url=?, github_url=?, tags=? WHERE id=?");
        if ($stmt->execute([$titulo, $descripcion, $imagen_url, $demo_url, $github_url, $tags, $id])) {
            $mensaje = '<div class="alert alert-success">Proyecto actualizado</div>';
        }
    }
    elseif ($action === 'eliminar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $stmt = $db->prepare("DELETE FROM proyectos WHERE id=?");
        if ($stmt->execute([$id])) {
            $mensaje = '<div class="alert alert-success">Proyecto eliminado</div>';
        }
    }
}

// Obtener todos los proyectos
$proyectos = $db->query("SELECT * FROM proyectos ORDER BY fecha_creacion DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Proyectos - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-dark min-vh-100 p-0">
                <div class="text-center py-3 bg-primary">
                    <h5 class="text-white">Panel Admin</h5>
                    <small class="text-white-50">Bienvenido, <?php echo h($_SESSION['usuario']); ?></small>
                </div>
                <nav class="nav flex-column">
                    <a href="index.php" class="nav-link text-white">Dashboard</a>
                    <a href="habilidades.php" class="nav-link text-white">Habilidades</a>
                    <a href="tecnologias.php" class="nav-link text-white">Tecnologías</a>
                    <a href="proyectos.php" class="nav-link text-white bg-secondary">Proyectos</a>
                    <a href="contactos.php" class="nav-link text-white">Contactos</a>
                    <a href="../logout.php" class="nav-link text-white">Cerrar Sesión</a>
                </nav>
            </div>
            
            <!-- Content -->
            <div class="col-md-10 p-4">
                <h1>Gestionar Proyectos</h1>
                <?php echo $mensaje; ?>
                
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalProyecto" onclick="resetForm()">
                    <i class="fas fa-plus"></i> Nuevo Proyecto
                </button>
                
                <div class="row">
                    <?php foreach ($proyectos as $p): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo h($p['imagen_url']); ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo h($p['titulo']); ?></h5>
                                <p class="card-text"><?php echo h(substr($p['descripcion'], 0, 100)) . '...'; ?></p>
                                <div class="mb-2">
                                    <?php 
                                    $tags = explode(',', $p['tags']);
                                    foreach ($tags as $tag): ?>
                                        <span class="badge bg-secondary"><?php echo h(trim($tag)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-warning" onclick="editarProyecto(<?php echo htmlspecialchars(json_encode($p)); ?>)">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar este proyecto?')">
                                        <input type="hidden" name="action" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="modalProyecto" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Proyecto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formProyecto">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="crear">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label>Título</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>URL de Imagen</label>
                            <input type="url" name="imagen_url" id="imagen_url" class="form-control" placeholder="assets/img/proyecto.jpg">
                        </div>
                        <div class="mb-3">
                            <label>URL Demo</label>
                            <input type="url" name="demo_url" id="demo_url" class="form-control" placeholder="https://ejemplo.com">
                        </div>
                        <div class="mb-3">
                            <label>URL GitHub</label>
                            <input type="url" name="github_url" id="github_url" class="form-control" placeholder="https://github.com/usuario/proyecto">
                        </div>
                        <div class="mb-3">
                            <label>Tags (separados por coma)</label>
                            <input type="text" name="tags" id="tags" class="form-control" placeholder="PHP, MySQL, Bootstrap">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.getElementById('formProyecto').reset();
            document.getElementById('action').value = 'crear';
            document.getElementById('id').value = '';
        }
        
        function editarProyecto(data) {
            document.getElementById('action').value = 'editar';
            document.getElementById('id').value = data.id;
            document.getElementById('titulo').value = data.titulo;
            document.getElementById('descripcion').value = data.descripcion;
            document.getElementById('imagen_url').value = data.imagen_url;
            document.getElementById('demo_url').value = data.demo_url;
            document.getElementById('github_url').value = data.github_url;
            document.getElementById('tags').value = data.tags;
            
            new bootstrap.Modal(document.getElementById('modalProyecto')).show();
        }
    </script>
</body>
</html>