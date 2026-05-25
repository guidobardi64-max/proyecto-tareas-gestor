<?php
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$db = getDB();

// Obtener estadísticas
$total_habilidades = $db->query("SELECT COUNT(*) FROM habilidades")->fetchColumn();
$total_tecnologias = $db->query("SELECT COUNT(*) FROM tecnologias")->fetchColumn();
$total_proyectos = $db->query("SELECT COUNT(*) FROM proyectos")->fetchColumn();
$total_contactos = $db->query("SELECT COUNT(*) FROM contactos WHERE leido = FALSE")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar { min-height: 100vh; background-color: #030213; }
        .sidebar a { color: white; text-decoration: none; padding: 10px; display: block; }
        .sidebar a:hover { background-color: #495057; }
        .content { padding: 20px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="text-center py-3 bg-dark">
                    <h5 class="text-white">Panel Admin</h5>
                    <small class="text-white-50">Bienvenido, <?php echo h($_SESSION['usuario']); ?></small>
                </div>
                <nav class="nav flex-column">
                    <a href="index.php"><i class="fas fa-dashboard me-2"></i> Dashboard</a>
                    <a href="habilidades.php"><i class="fas fa-code me-2"></i> Habilidades</a>
                    <a href="tecnologias.php"><i class="fas fa-chart-line me-2"></i> Tecnologías</a>
                    <a href="proyectos.php"><i class="fas fa-project-diagram me-2"></i> Proyectos</a>
                    <a href="contactos.php"><i class="fas fa-envelope me-2"></i> Contactos</a>
                    <a href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a>
                </nav>
            </div>
            
            <!-- Content -->
            <div class="col-md-10 content">
                <h1 class="mb-4">Dashboard</h1>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Habilidades</h5>
                                <h2><?php echo $total_habilidades; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Tecnologías</h5>
                                <h2><?php echo $total_tecnologias; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Proyectos</h5>
                                <h2><?php echo $total_proyectos; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Mensajes Nuevos</h5>
                                <h2><?php echo $total_contactos; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Acciones Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group">
                            <a href="habilidades.php" class="btn btn-primary">Gestionar Habilidades</a>
                            <a href="tecnologias.php" class="btn btn-success">Gestionar Tecnologías</a>
                            <a href="proyectos.php" class="btn btn-info">Gestionar Proyectos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>