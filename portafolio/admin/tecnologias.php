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
        $nivel = filter_input(INPUT_POST, 'nivel', FILTER_VALIDATE_INT);
        $orden = filter_input(INPUT_POST, 'orden', FILTER_VALIDATE_INT);
        
        $stmt = $db->prepare("INSERT INTO tecnologias (nombre, nivel, orden) VALUES (?, ?, ?)");
        if ($stmt->execute([$nombre, $nivel, $orden])) {
            $mensaje = '<div class="alert alert-success">Tecnología creada exitosamente</div>';
        }
    } 
    elseif ($action === 'editar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $nivel = filter_input(INPUT_POST, 'nivel', FILTER_VALIDATE_INT);
        $orden = filter_input(INPUT_POST, 'orden', FILTER_VALIDATE_INT);
        
        $stmt = $db->prepare("UPDATE tecnologias SET nombre=?, nivel=?, orden=? WHERE id=?");
        if ($stmt->execute([$nombre, $nivel, $orden, $id])) {
            $mensaje = '<div class="alert alert-success">Tecnología actualizada</div>';
        }
    }
    elseif ($action === 'eliminar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $stmt = $db->prepare("DELETE FROM tecnologias WHERE id=?");
        if ($stmt->execute([$id])) {
            $mensaje = '<div class="alert alert-success">Tecnología eliminada</div>';
        }
    }
}

// Obtener todas las tecnologías
$tecnologias = $db->query("SELECT * FROM tecnologias ORDER BY orden")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Tecnologías - Admin</title>
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
                    <a href="tecnologias.php" class="nav-link text-white bg-secondary">Tecnologías</a>
                    <a href="proyectos.php" class="nav-link text-white">Proyectos</a>
                    <a href="contactos.php" class="nav-link text-white">Contactos</a>
                    <a href="../logout.php" class="nav-link text-white">Cerrar Sesión</a>
                </nav>
            </div>
            
            <!-- Content -->
            <div class="col-md-10 p-4">
                <h1>Gestionar Tecnologías</h1>
                <?php echo $mensaje; ?>
                
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTecnologia" onclick="resetForm()">
                    <i class="fas fa-plus"></i> Nueva Tecnología
                </button>
                
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Nivel (%)</th>
                            <th>Orden</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tecnologias as $t): ?>
                        <tr>
                            <td><?php echo $t['id']; ?></td>
                            <td><?php echo h($t['nombre']); ?></td>
                            <td>
                                <div class="progress" style="height: 30px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?php echo $t['nivel']; ?>%">
                                        <?php echo $t['nivel']; ?>%
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $t['orden']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editarTecnologia(<?php echo htmlspecialchars(json_encode($t)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta tecnología?')">
                                    <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
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
    
    <!-- Modal -->
    <div class="modal fade" id="modalTecnologia" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tecnología</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formTecnologia">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="crear">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Nivel (0-100)</label>
                            <input type="range" name="nivel" id="nivel" min="0" max="100" class="form-range" oninput="document.getElementById('nivelValue').innerText = this.value">
                            <span id="nivelValue" class="badge bg-primary">50</span>
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
            document.getElementById('formTecnologia').reset();
            document.getElementById('action').value = 'crear';
            document.getElementById('id').value = '';
        }
        
        function editarTecnologia(data) {
            document.getElementById('action').value = 'editar';
            document.getElementById('id').value = data.id;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('nivel').value = data.nivel;
            document.getElementById('nivelValue').innerText = data.nivel;
            document.getElementById('orden').value = data.orden;
            
            new bootstrap.Modal(document.getElementById('modalTecnologia')).show();
        }
    </script>
</body>
</html>