<?php
session_start();

// Si no está logueado, redirigir
if (!isset($_SESSION['documento'])) {
    header("Location: ../login/login.php");
    exit();
}

$documento = $_SESSION['documento'];
$nombreUsuario = $_SESSION['nombre'];

// Conexión a la BD
$conn = new mysqli("localhost", "root", "", "viviendas");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el usuario tiene comprobante aprobado
$query = "SELECT estado FROM comprobante_pago 
          WHERE documento = ? AND tipo='inicial' 
          ORDER BY id_comprobante DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $documento);
$stmt->execute();
$result = $stmt->get_result();

$acceso = false;
if ($row = $result->fetch_assoc()) {
    if ($row['estado'] === 'aprobado') {
        $acceso = true;
    }
}
$stmt->close();
$conn->close();

if (!$acceso) {
    header("Location: subir_comprobante.php");
    exit();
}
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
            <li><a href="../login/logout.php" class="logout">Cerrar sesión</a></li>
        </ul>
    </aside>

    <div class="background"></div>

    <main class="dashboard">

        <section class="strip">
            <h2>Bienvenido,
                <?php echo htmlspecialchars($nombreUsuario); ?>
            </h2>
            <p>Bienvenido a <strong>Nuevo Amanecer</strong>. Desde aquí podrás subir tus comprobantes, consultar tus
                pagos y conocer tu unidad habitacional</p>
        </section>

        <section class="highlight">
            <div>
                <i class="fi fi-sr-document"></i>
                <a href="pagos.php" class="btn-hover">Subir comprobante</a>
            </div>
        </section>

        <section class="strip">
            <h2>Estado de tu último comprobante</h2>
            <p><strong>Tu comprobante inicial fue aprobado</strong></p>
        </section>

        <section class="strip alt">
            <h2>Recordatorios</h2>
            <ul>
                <li>Próxima cuota: <strong>15/09/2025</strong></li>
                <li>Reunión mensual: <strong>20/09/2025 a las 18:00hs</strong></li>
            </ul>
        </section>
    </main>

    <script src="script.js"></script>
</body>

</html>