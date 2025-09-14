<?php
session_start();

// Si el usuario no está logueado, redirigir
if (!isset($_SESSION['documento'])) {
    header("Location: ../login/login.php");
    exit();
}

// Nombre del usuario desde la sesión
$nombreUsuario = $_SESSION['nombre'];
$documento = $_SESSION['documento'];
$mensaje = "";

// Conexión con la BD
$conn = new mysqli("localhost", "root", "", "viviendas");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["comprobante"])) {
    $archivo = $_FILES["comprobante"];

    // Verificar extensión
    $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));
    if ($ext !== "pdf") {
        $mensaje = "Solo se permiten archivos PDF.";
    } else {
        // Carpeta absoluta
        $carpeta = "C:/xampp/htdocs/proyecto-coop/uploads/inicial/";
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        // Nombre único
        $nombreArchivo = $documento . "_" . time() . ".pdf";
        $ruta = $carpeta . $nombreArchivo;

        if (move_uploaded_file($archivo["tmp_name"], $ruta)) {
            $rutaBD = "uploads/inicial/" . $nombreArchivo;

            // Guardar comprobante como tipo "inicial"
            $stmt = $conn->prepare("INSERT INTO comprobante_pago (documento, archivo_pdf, estado, tipo) VALUES (?, ?, 'pendiente', 'inicial')");
            $stmt->bind_param("ss", $documento, $rutaBD);

            if ($stmt->execute()) {
                $mensaje = "✅ Comprobante inicial subido con éxito. Estado: pendiente de aprobación.";
            } else {
                $mensaje = "⚠️ Error al guardar en la base de datos.";
            }
            $stmt->close();
        } else {
            $mensaje = "⚠️ Error al mover el archivo.";
        }
    }
}
$conn->close();
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
            <li><a href="primera.php">Inicio</a></li>
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

       <div class="highlight">
            <div>
                

                <?php if ($mensaje): ?>
                    <p><strong><?php echo $mensaje; ?></strong></p>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <label>Selecciona tu comprobante (PDF):</label>
                    <input type="file" name="comprobante" accept="application/pdf" required>
                    <button type="submit"> Subir</button>
                </form>

                <br>
                <a href="primera.php" class="btn-hover2">Volver al inicio</a>
            </div>
        </div>
    </div>
    </main>

    <script src="script.js"></script>
</body>

</html>
