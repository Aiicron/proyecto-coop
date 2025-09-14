<?php
session_start();

// Si el usuario no está logueado, redirigir
if (!isset($_SESSION['documento'])) {
    header("Location: ../login/login.php");
    exit();
}

// Nombre del usuario desde la sesión
$nombreUsuario = $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Usuario - Bienvenida</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Barra de navegación -->
    <nav>
        <div class="nav-content">
            <div class="hamburger" id="hamburger">
                <span></span><span></span><span></span>
            </div>

            <div class="nav-center">
                <h2 class="nav-title">Nuevo Amanecer</h2>
            </div>

            <div class="nav-right">
                <img src="logonuevo.png" class="logo1" alt="Logo cooperativa">
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <h2>Menú</h2>
        <ul>
            <li><a href="bienvenida.php">Inicio</a></li>
            <li><a href="perfil.php">Mi perfil</a></li>
            <li><a href="../login/logout.php" class="logout">Cerrar sesión</a></li>
        </ul>
    </aside>

    <!-- Fondo -->
    <div class="background"></div>

    <!-- Contenido principal -->
    <main class="dashboard">
        <!-- Sección bienvenida -->
        <section class="strip">
            <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
            <p>Para que se te asigne una unidad habitacional deberás subir el comprobante de <strong>pago inicial</strong>.</p>
        </section>

        <!-- Atajo rápido solo a pagos -->
        <section class="highlight">
            <div>
                <i class="fi fi-sr-document"></i>
                <a href="pagos.php" class="btn-hover">Subir comprobante inicial</a>
            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>

</html>
