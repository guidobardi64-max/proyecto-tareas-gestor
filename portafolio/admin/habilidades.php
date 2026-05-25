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
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $icono = filter_input(INPUT_POST, 'icono', FILTER_SANITIZE_STRING);
        $color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_STRING);
        $orden = filter_input(INPUT_POST, 'orden', FILTER_VALIDATE_INT);
        
        $stmt = $db->prepare("INSERT INTO habilidades (nombre, icono, color, orden) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nombre, $icono, $color, $orden])) {
            $mensaje = '<div class="alert alert-success">Habilidad creada exitosamente</div>';
        }
    } 
    elseif ($action === 'editar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $icono = filter_input(INPUT_POST, 'icono', FILTER_SANITIZE_STRING);
        $color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_STRING);
        $orden = filter_input(INPUT_POST, 'orden', FILTER_VALIDATE_INT);
        
        $stmt = $db->prepare("UPDATE habilidades SET nombre=?, icono=?, color=?, orden=? WHERE id=?");
        if ($stmt->execute([$nombre, $icono, $color, $orden, $id])) {
            $mensaje = '<div class="alert alert-success">Habilidad actualizada</div>';
        }
    }
    elseif ($action === 'eliminar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $stmt = $db->prepare("DELETE FROM habilidades WHERE id=?");
        if ($stmt->execute([$id])) {
            $mensaje = '<div class="alert alert-success">Habilidad eliminada</div>';
        }
    }
}

// Obtener todas las habilidades
$habilidades = $db->query("SELECT * FROM habilidades ORDER BY orden")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Habilidades - Admin</title>
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
                    <a href="habilidades.php" class="nav-link text-white bg-secondary">Habilidades</a>
                    <a href="tecnologias.php" class="nav-link text-white">Tecnologías</a>
                    <a href="proyectos.php" class="nav-link text-white">Proyectos</a>
                    <a href="contactos.php" class="nav-link text-white">Contactos</a>
                    <a href="../logout.php" class="nav-link text-white">Cerrar Sesión</a>
                </nav>
            </div>
            
            <!-- Content -->
            <div class="col-md-10 p-4">
                <h1>Gestionar Habilidades</h1>
                <?php echo $mensaje; ?>
                
                <!-- Botón para agregar -->
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalHabilidad" onclick="resetForm()">
                    <i class="fas fa-plus"></i> Nueva Habilidad
                </button>
                
                <!-- Tabla de habilidades -->
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Icono</th>
                            <th>Nombre</th>
                            <th>Color</th>
                            <th>Orden</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($habilidades as $h): ?>
                        <tr>
                            <td><?php echo $h['id']; ?></td>
                            <td><i class="<?php echo h($h['icono']); ?> fa-2x"></i></td>
                            <td><?php echo h($h['nombre']); ?></td>
                            <td>
                                <div style="width: 30px; height: 30px; background-color: <?php echo h($h['color']); ?>; border: 1px solid #ddd;"></div>
                                <?php echo h($h['color']); ?>
                            </td>
                            <td><?php echo $h['orden']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editarHabilidad(<?php echo htmlspecialchars(json_encode($h)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta habilidad?')">
                                    <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" name="id" value="<?php echo $h['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal para crear/editar -->
    <div class="modal fade" id="modalHabilidad" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Habilidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formHabilidad">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="crear">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Icono (clase Font Awesome)</label>
                            <input type="text" name="icono" id="icono" class="form-control" placeholder="fab fa-php" required>
                            <small>Ejemplos: fab fa-html5, fab fa-java, fab fa-python</small>
                        </div>
                        <div class="mb-3">
                            <label>Color</label>
                            <input type="color" name="color" id="color" class="form-control" value="#030213">
                        </div>
                        <div class="mb-3">
                            <label>Orden</label>
                            <input type="number" name="orden" id="orden" class="form-control" required>
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
            document.getElementById('formHabilidad').reset();
            document.getElementById('action').value = 'crear';
            document.getElementById('id').value = '';
        }
        
        function editarHabilidad(data) {
            document.getElementById('action').value = 'editar';
            document.getElementById('id').value = data.id;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('icono').value = data.icono;
            document.getElementById('color').value = data.color;
            document.getElementById('orden').value = data.orden;
            
            new bootstrap.Modal(document.getElementById('modalHabilidad')).show();
        }
    </script>
</body>
</html>