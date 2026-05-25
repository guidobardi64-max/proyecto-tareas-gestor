<?php
// Detectar si hay sesión iniciada
$isLoggedIn = isset($_SESSION['user_id']);
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/portafolio-web/">
            <i class="fas fa-code"></i> Guido Bardi
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>" href="../index.php#inicio">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php#biografia">Biografía</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php#habilidades">Habilidades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php#tecnologias">Tecnologías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php#proyectos">Proyectos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php#contacto">Contacto</a>
                </li>
                <?php if ($isLoggedIn): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-shield"></i> Admin
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../admin/">Dashboard</a></li>
                        <li><a class="dropdown-item" href="../admin/habilidades.php">Habilidades</a></li>
                        <li><a class="dropdown-item" href="../admin/tecnologias.php">Tecnologías</a></li>
                        <li><a class="dropdown-item" href="../admin/proyectos.php">Proyectos</a></li>
                        <li><a class="dropdown-item" href="../admin/contactos.php">Contactos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php">Cerrar Sesión</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="../login.php">
                        <i class="fas fa-lock"></i> Admin
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>