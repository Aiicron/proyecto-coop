<?php
session_start();

// Si el usuario no está logueado, redirigir
if (!isset($_SESSION['documento'])) {
    header("Location: ../login/login.php");
    exit();
}

$nombreUsuario = $_SESSION['nombre'];
$documento = $_SESSION['documento'];
$mensaje = "";

// Conexión con la BD
$conn = new mysqli("localhost", "root", "", "viviendas");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Subida de comprobante
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["comprobante"])) {
    $archivo = $_FILES["comprobante"];
    $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));

    if ($ext !== "pdf") {
        $mensaje = "Solo se permiten archivos PDF.";
    } else {
        $carpeta = "C:/xampp/htdocs/proyecto-coop/uploads/inicial/";
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $nombreArchivo = $documento . "_" . time() . ".pdf";
        $ruta = $carpeta . $nombreArchivo;

        if (move_uploaded_file($archivo["tmp_name"], $ruta)) {
            $rutaBD = "uploads/inicial/" . $nombreArchivo;

            $stmt = $conn->prepare("INSERT INTO comprobante_pago (documento, archivo_pdf, estado, tipo) VALUES (?, ?, 'pendiente', 'inicial')");
            $stmt->bind_param("ss", $documento, $rutaBD);

            if ($stmt->execute()) {
                $mensaje = "Comprobante inicial subido con éxito";
            } else {
                $mensaje = "Error al guardar en la base de datos.";
            }
            $stmt->close();
        } else {
            $mensaje = "Error al mover el archivo.";
        }
    }
}

// Consultar último estado del comprobante
$estado = null;
$sqlEstado = "SELECT estado FROM comprobante_pago WHERE documento='$documento' AND tipo='inicial' ORDER BY id_comprobante DESC LIMIT 1";
$resEstado = $conn->query($sqlEstado);

if ($resEstado && $resEstado->num_rows > 0) {
    $estado = $resEstado->fetch_assoc()['estado'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Usuario - Subir Comprobante</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .estado-msg {
            font-weight: bold;
            margin-top: 10px;
        }


        /* Ajuste para el botón de ingresar */
        .btn-ingresar {
            display: inline-block;
            text-align: center;
            padding: 10px 20px;
            background-color: #27AE60;
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn-ingresar:hover {
            background-color: #219150;
        }
    </style>
</head>

<body>
    <nav>
        <div class="nav-content">
            <div class="nav-center">
                <h2 class="nav-title">Nuevo Amanecer</h2>
            </div>
            <div class="nav-right">
                <img src="logonuevo.png" class="logo1" alt="Logo cooperativa">
            </div>
        </div>
    </nav>


    <div class="background"></div>

    <main class="dashboard">
        <section class="strip">
            <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
            <p>Para que se te asigne una unidad habitacional deberás subir el comprobante de <strong>pago
                    inicial</strong>.</p>
        </section>

        <div class="highlight">
            <?php if ($mensaje): ?>
                <p><strong><?php echo $mensaje; ?></strong></p>
            <?php endif; ?>

            <?php if ($estado !== "aprobado"): ?>
                <form method="POST" enctype="multipart/form-data">
                    <label>Selecciona tu comprobante (PDF):</label>
                    <button type="button" class="btn-hover" onclick="document.getElementById('fileInput').click();">
                        Subir comprobante inicial
                    </button>
                    <input type="file" id="fileInput" name="comprobante" accept="application/pdf" style="display:none;"
                        required onchange="this.form.submit();">
                </form>
            <?php endif; ?>

            <?php if ($estado === "pendiente"): ?>
                <p class="estado-msg pendiente"> Tu comprobante está pendiente de aprobación.</p>
            <?php elseif ($estado === "rechazado"): ?>
                <p class="estado-msg rechazado"> Tu comprobante fue rechazado. Sube uno nuevo.</p>
            <?php elseif ($estado === "aprobado"): ?>
                <p class="estado-msg aprobado"> Tu comprobante fue aprobado. Ya puedes ingresar a la cooperativa.</p>
                <a href="../frontend-coop/bienvenida.php" class="btn-aceptar btn-ingresar">Ingresar a la Cooperativa</a>
            <?php endif; ?>
        </div>
    </main>

    <script src="script.js"></script>
</body>

</html>