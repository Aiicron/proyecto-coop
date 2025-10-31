<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis pagos</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css">
    <link rel="stylesheet" href="assets/front.css">
    <style>
        .estado-msg {
            font-weight: bold;
        }

        .tabla-pagos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .tabla-pagos th,
        .tabla-pagos td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        .tabla-pagos th {
            background-color: #2C3E50;
            color: white;
            font-weight: 600;
        }

        .tabla-pagos tr:hover {
            background-color: #f5f5f5;
        }

        .pendiente {
            color: orange;
            font-weight: bold;
        }

        .aprobado {
            color: green;
            font-weight: bold;
        }

        .rechazado {
            color: red;
            font-weight: bold;
        }

        .tabla-pagos a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }

        .tabla-pagos a:hover {
            text-decoration: underline;
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
                <img src="assets/logonuevo.png" class="logo1" alt="Logo cooperativa">
            </div>
        </div>
    </nav>

    <aside class="sidebar" id="sidebar">
        <h2>Menú</h2>
        <ul>
            <li><a href="index.php?page=bienvenida">Inicio</a></li>
            <li><a href="index.php?page=pagos">Mis pagos</a></li>
            <li><a href="index.php?page=horas">Horas</a></li>
            <li><a href="index.php?page=perfil">Mi perfil</a></li>
            <li><a href="index.php?accion=logout" class="logout">Cerrar sesión</a></li>
        </ul>
    </aside>

    <div class="background"></div>

    <main class="dashboard">
        <!-- PRIMERO: Formulario de subida -->
        <section class="strip">
            <h2>Subir nuevo comprobante mensual</h2>

            <?php if ($mensaje): ?>
                <p class="estado-msg"><strong><?php echo htmlspecialchars($mensaje); ?></strong></p>
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

        <!-- SEGUNDO: Historial completo -->
        <section class="strip alt">
            <h2>Historial completo de pagos</h2>
            <?php if (count($historial) > 0): ?>
                <table class="tabla-pagos">
                    <thead>
                        <tr>
                            <th>Fecha de subida</th>
                            <th>Archivo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $pago): ?>
                            <tr>
                                <td><?php echo date("d/m/Y H:i", strtotime($pago['fecha_subida'])); ?></td>
                                <td><a href="../<?php echo htmlspecialchars($pago['archivo_pdf']); ?>" target="_blank">Ver comprobante</a></td>
                                <td class="<?php echo htmlspecialchars($pago['estado']); ?>"><?php echo ucfirst($pago['estado']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No has subido comprobantes mensuales todavía.</p>
            <?php endif; ?>
        </section>
    </main>

    <script src="assets/ham.js"></script>
</body>

</html>