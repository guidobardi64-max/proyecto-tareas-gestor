<?php
require_once 'config/database.php';
$db = getDB();

// Obtener datos para la página pública
$biografia = $db->query("SELECT * FROM biografia LIMIT 1")->fetch();
$habilidades = $db->query("SELECT * FROM habilidades ORDER BY orden")->fetchAll();
$tecnologias = $db->query("SELECT * FROM tecnologias ORDER BY orden")->fetchAll();
$proyectos = $db->query("SELECT * FROM proyectos ORDER BY fecha_creacion DESC")->fetchAll();

// Procesar formulario de contacto
$mensaje_contacto = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'contacto') {
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $asunto = filter_input(INPUT_POST, 'asunto', FILTER_SANITIZE_STRING);
    $mensaje = filter_input(INPUT_POST, 'mensaje', FILTER_SANITIZE_STRING);
    
    if ($nombre && $email && $asunto && $mensaje) {
        $stmt = $db->prepare("INSERT INTO contactos (nombre, email, asunto, mensaje) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nombre, $email, $asunto, $mensaje])) {
            $mensaje_contacto = '<div class="alert alert-success">¡Mensaje enviado con éxito!</div>';
        } else {
            $mensaje_contacto = '<div class="alert alert-danger">Error al enviar el mensaje.</div>';
        }
    } else {
        $mensaje_contacto = '<div class="alert alert-danger">Por favor, complete todos los campos.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($biografia['nombre']); ?> - Portafolio Profesional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">GB</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#inicio">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#biografia">Biografía</a></li>
                    <li class="nav-item"><a class="nav-link" href="#habilidades">Habilidades</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tecnologias">Tecnologías</a></li>
                    <li class="nav-item"><a class="nav-link" href="#proyectos">Proyectos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Inicio -->
    <section id="inicio" class="hero-section">
        <div class="container text-center">
            <div class="hero-content">
                <h1 class="display-4 fade-in"><?php echo h($biografia['nombre']); ?></h1>
                <p class="lead slide-in"><?php echo h($biografia['titulo']); ?></p>
                <a href="#contacto" class="btn btn-primary btn-lg">Contáctame</a>
            </div>
        </div>
    </section>

    <!-- Biografía -->
    <section id="biografia" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Biografía</h2>
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <img src="<?php echo h($biografia['foto_url']); ?>" alt="Avatar" class="rounded-circle img-fluid mb-3" style="max-width: 250px;">
                </div>
                <div class="col-md-8">
                    <h3><?php echo h($biografia['nombre']); ?></h3>
                    <p class="lead"><?php echo h($biografia['titulo']); ?></p>
                    <p><?php echo nl2br(h($biografia['descripcion'])); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Habilidades -->
    <section id="habilidades" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Habilidades</h2>
            <div class="row">
                <?php foreach ($habilidades as $habilidad): ?>
                <div class="col-md-3 mb-4">
                    <div class="card skill-card h-100 text-center">
                        <div class="card-body">
                            <i class="<?php echo h($habilidad['icono']); ?> fa-3x mb-3" style="color: <?php echo h($habilidad['color']); ?>"></i>
                            <h5 class="card-title"><?php echo h($habilidad['nombre']); ?></h5>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Tecnologías -->
    <section id="tecnologias" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Tecnologías</h2>
            <div class="row">
                <?php foreach ($tecnologias as $tecnologia): ?>
                <div class="col-md-6 mb-4">
                    <div class="tech-item">
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo h($tecnologia['nombre']); ?></span>
                            <span><?php echo $tecnologia['nivel']; ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: <?php echo $tecnologia['nivel']; ?>%"
                                 aria-valuenow="<?php echo $tecnologia['nivel']; ?>" 
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Proyectos -->
    <section id="proyectos" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Proyectos</h2>
            <div class="row">
                <?php foreach ($proyectos as $proyecto): ?>
                <div class="col-md-6 mb-4">
                    <div class="card project-card h-100">
                        <img src="<?php echo h($proyecto['imagen_url']); ?>" class="card-img-top" alt="<?php echo h($proyecto['titulo']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo h($proyecto['titulo']); ?></h5>
                            <p class="card-text"><?php echo h($proyecto['descripcion']); ?></p>
                            <div class="mb-2">
                                <?php 
                                $tags = explode(',', $proyecto['tags']);
                                foreach ($tags as $tag): ?>
                                    <span class="badge bg-secondary me-1"><?php echo h(trim($tag)); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="btn-group">
                                <?php if ($proyecto['demo_url'] != '#'): ?>
                                <a href="<?php echo h($proyecto['demo_url']); ?>" class="btn btn-primary" target="_blank">Demo</a>
                                <?php endif; ?>
                                <?php if ($proyecto['github_url'] != '#'): ?>
                                <a href="<?php echo h($proyecto['github_url']); ?>" class="btn btn-dark" target="_blank">GitHub</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section id="contacto" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Contacto</h2>
            <?php echo $mensaje_contacto; ?>
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="contacto">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="asunto" class="form-label">Asunto</label>
                            <input type="text" class="form-control" id="asunto" name="asunto" required>
                        </div>
                        <div class="mb-3">
                            <label for="mensaje" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Enviar Mensaje</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <div class="mb-3">
                <a href="https://github.com" class="text-white me-3" target="_blank"><i class="fab fa-github fa-2x"></i></a>
                <a href="https://linkedin.com" class="text-white me-3" target="_blank"><i class="fab fa-linkedin fa-2x"></i></a>
                <a href="mailto:Guidobardi64@gmail.com" class="text-white"><i class="fas fa-envelope fa-2x"></i></a>
            </div>
            <p>&copy; 2024 <?php echo h($biografia['nombre']); ?>. Todos los derechos reservados.</p>
            <p>Email: Guidobardi64@gmail.com</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>