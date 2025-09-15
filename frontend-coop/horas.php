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

// Conexión BD
$conn = new mysqli("localhost", "root", "", "viviendas");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Guardar horas y posible justificativo/pago compensatorio
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $horas = (int) $_POST["horas"];
    $motivo = isset($_POST["motivo"]) ? $_POST["motivo"] : null;

    // Insertar horas trabajadas
    $stmt = $conn->prepare("INSERT INTO horas_trabajadas (documento, horas, fecha, motivo) VALUES (?, ?, CURDATE(), ?)");
    $stmt->bind_param("sis", $documento, $horas, $motivo);
    $stmt->execute();
    $stmt->close();

    // Si no completó las 21 horas → puede subir comprobante compensatorio
    if ($horas < 21 && isset($_FILES["compensatorio"]) && $_FILES["compensatorio"]["error"] == 0) {
        $archivo = $_FILES["compensatorio"];
        $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));

        if ($ext === "pdf") {
            $carpeta = "C:/xampp/htdocs/proyecto-coop/uploads/compensatorio/";
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $nombreArchivo = $documento . "_comp_" . time() . ".pdf";
            $ruta = $carpeta . $nombreArchivo;

            if (move_uploaded_file($archivo["tmp_name"], $ruta)) {
                $rutaBD = "uploads/compensatorio/" . $nombreArchivo;

                $stmt = $conn->prepare("INSERT INTO comprobante_pago (documento, archivo_pdf, estado, tipo, fecha_subida) 
                                        VALUES (?, ?, 'pendiente', 'compensatorio', NOW())");
                $stmt->bind_param("ss", $documento, $rutaBD);
                $stmt->execute();
                $stmt->close();

                $mensaje = "✅ Horas y comprobante compensatorio registrados con éxito.";
            } else {
                $mensaje = "⚠️ Error al mover el comprobante compensatorio.";
            }
        } else {
            $mensaje = "⚠️ El comprobante compensatorio debe ser un PDF.";
        }
    } else {
        $mensaje = "✅ Horas registradas correctamente.";
    }
}

// Consultar historial de horas
$historialHoras = [];
$sql = "SELECT horas, fecha, motivo FROM horas_trabajadas WHERE documento='$documento' ORDER BY fecha DESC";
$res = $conn->query($sql);
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $historialHoras[] = $row;
    }
}

// Consultar historial de comprobantes compensatorios
$historialComp = [];
$sqlComp = "SELECT archivo_pdf, estado, fecha_subida 
            FROM comprobante_pago 
            WHERE documento='$documento' AND tipo='compensatorio' 
            ORDER BY fecha_subida DESC";
$resComp = $conn->query($sqlComp);
if ($resComp && $resComp->num_rows > 0) {
    while ($row = $resComp->fetch_assoc()) {
        $historialComp[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis horas</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .tabla th, .tabla td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        .tabla th {
            background-color: #f2f2f2;
        }
        .pendiente { color: orange; }
        .aprobado { color: green; }
        .rechazado { color: red; }
    </style>
</head>
<body>
    <nav>
        <div class="nav-content">
            <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
            <div class="nav-center"><h2 class="nav-title">Nuevo Amanecer</h2></div>
            <div class="nav-right"><img src="logonuevo.png" class="logo1" alt="Logo cooperativa"></div>
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

<!-- Registrar nuevas horas -->
        <section class="strip alt">
            <h2>Registrar nuevas horas</h2>
            <?php if ($mensaje): ?>
                <p><strong><?php echo $mensaje; ?></strong></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <label>Cantidad de horas trabajadas:</label>
                <input type="number" name="horas" min="0" max="40" required>

                <label>Motivo (solo si no llegaste a 21 horas):</label>
                <textarea name="motivo" rows="3" placeholder="Explica tu motivo..."></textarea>

                <label>Comprobante de pago compensatorio (PDF, opcional si no cumpliste las horas):</label>
                <input type="file" name="compensatorio" accept="application/pdf">

                <button type="submit">Registrar</button>
            </form>
        </section>

        <!-- Historial de horas -->
        <section class="strip">
            <h2>Historial de horas</h2>
            <?php if (count($historialHoras) > 0): ?>
                <table class="tabla">
                    <tr>
                        <th>Fecha</th>
                        <th>Horas</th>
                        <th>Motivo</th>
                    </tr>
                    <?php foreach ($historialHoras as $h): ?>
                        <tr>
                            <td><?php echo date("d/m/Y", strtotime($h['fecha'])); ?></td>
                            <td><?php echo $h['horas']; ?></td>
                            <td><?php echo $h['motivo'] ? $h['motivo'] : "-"; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No has registrado horas todavía.</p>
            <?php endif; ?>
        </section>

        <!-- Historial de comprobantes compensatorios -->
        <section class="strip">
            <h2>Comprobantes compensatorios</h2>
            <?php if (count($historialComp) > 0): ?>
                <table class="tabla">
                    <tr>
                        <th>Fecha de subida</th>
                        <th>Archivo</th>
                        <th>Estado</th>
                    </tr>
                    <?php foreach ($historialComp as $comp): ?>
                        <tr>
                            <td><?php echo date("d/m/Y H:i", strtotime($comp['fecha_subida'])); ?></td>
                            <td><a href="../<?php echo $comp['archivo_pdf']; ?>" target="_blank">Ver comprobante</a></td>
                            <td class="<?php echo $comp['estado']; ?>"><?php echo ucfirst($comp['estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No has subido comprobantes compensatorios.</p>
            <?php endif; ?>
        </section>

        
    </main>
    <script src="script.js"></script>
</body>
</html>
