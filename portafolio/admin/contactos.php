<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$db = getDB();
$mensaje = '';

// Marcar como leído
if (isset($_GET['marcar_leido'])) {
    $id = filter_input(INPUT_GET, 'marcar_leido', FILTER_VALIDATE_INT);
    $stmt = $db->prepare("UPDATE contactos SET leido = TRUE WHERE id = ?");
    $stmt->execute([$id]);
    $mensaje = '<div class="alert alert-success">Mensaje marcado como leído</div>';
}

// Eliminar mensaje
if (isset($_GET['eliminar'])) {
    $id = filter_input(INPUT_GET, 'eliminar', FILTER_VALIDATE_INT);
    $stmt = $db->prepare("DELETE FROM contactos WHERE id = ?");
    $stmt->execute([$id]);
    $mensaje = '<div class="alert alert-success">Mensaje eliminado</div>';
}

// Obtener contactos
$contactos = $db->query("SELECT * FROM contactos ORDER BY fecha_envio DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Contactos - Admin</title>
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
                    <a href="proyectos.php" class="nav-link text-white">Proyectos</a>
                    <a href="contactos.php" class="nav-link text-white bg-secondary">Contactos</a>
                    <a href="../logout.php" class="nav-link text-white">Cerrar Sesión</a>
                </nav>
            </div>
            
            <!-- Content -->
            <div class="col-md-10 p-4">
                <h1>Mensajes de Contacto</h1>
                <?php echo $mensaje; ?>
                
                <div class="row">
                    <?php foreach ($contactos as $c): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card <?php echo !$c['leido'] ? 'border-primary' : ''; ?>">
                            <div class="card-header <?php echo !$c['leido'] ? 'bg-primary text-white' : 'bg-light'; ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo h($c['nombre']); ?></strong> 
                                        <small>(<?php echo h($c['email']); ?>)</small>
                                    </div>
                                    <small><?php echo date('d/m/Y H:i', strtotime($c['fecha_envio'])); ?></small>
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title"><?php echo h($c['asunto']); ?></h6>
                                <p class="card-text"><?php echo nl2br(h($c['mensaje'])); ?></p>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group">
                                    <?php if (!$c['leido']): ?>
                                    <a href="?marcar_leido=<?php echo $c['id']; ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Marcar como leído
                                    </a>
                                    <?php endif; ?>
                                    <a href="mailto:<?php echo h($c['email']); ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-reply"></i> Responder
                                    </a>
                                    <a href="?eliminar=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este mensaje?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($contactos)): ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            No hay mensajes de contacto
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>