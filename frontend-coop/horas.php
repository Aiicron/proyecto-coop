<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis horas</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
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

    <aside class="sidebar" id="sidebar">
        <h2>Menú</h2>
        <ul>
            <li><a href="bienvenida.php">Inicio</a></li>
            <li><a href="pagos.php">Mis pagos</a></li>
            <li><a href="horas.php">Horas</a></li>
            <li><a href="perfil.php">Mi perfil</a></li>
            <li><a href="#" class="logout">Cerrar sesión</a></li>
        </ul>
    </aside>

    <div class="background"></div>

    <main class="dashboard">
        <section class="strip">
            <h2>Mis horas de trabajo</h2>
            <p><strong>Septiembre 2025:</strong> 20 horas</p>
            <p><strong>Agosto 2025:</strong> 18 horas</p>
            <p><strong>Julio 2025:</strong> 22 horas</p>
        </section>
    </main>

    <script src="script.js"></script>
</body>

</html>