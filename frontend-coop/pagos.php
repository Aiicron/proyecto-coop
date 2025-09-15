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

// Subida de comprobante mensual
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["comprobante"])) {
    $archivo = $_FILES["comprobante"];
    $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));

    if ($ext !== "pdf") {
        $mensaje = "Solo se permiten archivos PDF.";
    } else {
        $carpeta = "C:/xampp/htdocs/proyecto-coop/uploads/mensual/";
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $nombreArchivo = $documento . "_" . time() . ".pdf";
        $ruta = $carpeta . $nombreArchivo;

        if (move_uploaded_file($archivo["tmp_name"], $ruta)) {
            $rutaBD = "uploads/mensual/" . $nombreArchivo;

            $stmt = $conn->prepare("INSERT INTO comprobante_pago (documento, archivo_pdf, estado, tipo, fecha_subida) 
                                    VALUES (?, ?, 'pendiente', 'mensual', NOW())");
            $stmt->bind_param("ss", $documento, $rutaBD);

            if ($stmt->execute()) {
                $mensaje = "Comprobante mensual subido con éxito";
            } else {
                $mensaje = "Error al guardar en la base de datos.";
            }
            $stmt->close();
        } else {
            $mensaje = "Error al mover el archivo.";
        }
    }
}

// Consultar historial de comprobantes mensuales
$historial = [];
$sqlHistorial = "SELECT archivo_pdf, estado, fecha_subida 
                 FROM comprobante_pago 
                 WHERE documento='$documento' AND tipo='mensual' 
                 ORDER BY fecha_subida DESC";
$resHistorial = $conn->query($sqlHistorial);

if ($resHistorial && $resHistorial->num_rows > 0) {
    while ($row = $resHistorial->fetch_assoc()) {
        $historial[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis pagos</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .estado-msg {
            font-weight: bold;
        }

        .tabla-pagos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .tabla-pagos th,
        .tabla-pagos td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        .tabla-pagos th {
            background-color: #f2f2f2;
        }

        .pendiente {
            color: orange;
        }

        .aprobado {
            color: green;
        }

        .rechazado {
            color: red;
        }
    </style>
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
            <h2>Historial de pagos</h2>
            <?php if (count($historial) > 0): ?>
                <table class="tabla-pagos">
                    <tr>
                        <th>Fecha de subida</th>
                        <th>Archivo</th>
                        <th>Estado</th>
                    </tr>
                    <?php foreach ($historial as $pago): ?>
                        <tr>
                            <td><?php echo date("d/m/Y H:i", strtotime($pago['fecha_subida'])); ?></td>
                            <td><a href="../<?php echo $pago['archivo_pdf']; ?>" target="_blank">Ver comprobante</a></td>
                            <td class="<?php echo $pago['estado']; ?>"><?php echo ucfirst($pago['estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No has subido comprobantes mensuales todavía.</p>
            <?php endif; ?>
        </section>

        <section class="strip alt">
            <h2>Subir nuevo comprobante mensual</h2>

            <?php if ($mensaje): ?>
                <p><strong><?php echo $mensaje; ?></strong></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <label>Selecciona tu comprobante (PDF):</label>
                <button type="button" class="btn-hover" onclick="document.getElementById('fileInput').click();">
                    Subir comprobante mensual
                </button>
                <input type="file" id="fileInput" name="comprobante" accept="application/pdf" style="display:none;"
                    required onchange="this.form.submit();">
            </form>
        </section>
    </main>

    <script src="script.js"></script>
</body>

</html>