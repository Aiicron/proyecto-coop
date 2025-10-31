<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Usuario - Subir Comprobante</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/front.css">
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
                <img src="assets/logonuevo.png" class="logo1" alt="Logo cooperativa">
            </div>
        </div>
    </nav>

    <aside class="sidebar" id="sidebar">
        <h2>Menú</h2>
        <ul>
            <li><a href="index.php?page=perfil">Mi perfil</a></li>
            <li><a href="index.php?accion=logout" class="logout">Cerrar sesión</a></li>
        </ul>
    </aside>

    <div class="background"></div>

    <main class="dashboard">
        <section class="strip">
            <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
            <p>Para que se te asigne una unidad habitacional deberás subir el comprobante de <strong>pago
                    inicial</strong>.</p>
        </section>

        <div class="highlight">
            <?php if ($mensaje): ?>
                <p><strong><?php echo htmlspecialchars($mensaje); ?></strong></p>
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
                <a href="index.php?page=bienvenida" class="btn-aceptar btn-ingresar">Ingresar a la Cooperativa</a>
            <?php endif; ?>
        </div>
    </main>

    <script src="assets/script.js"></script>
</body>

</html>