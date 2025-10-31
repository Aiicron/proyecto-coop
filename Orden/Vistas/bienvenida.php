<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Usuario - Bienvenida</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css">
    <link rel="stylesheet" href="assets/front.css">
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

        <section class="strip">
            <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
            <p>Bienvenido a <strong>Nuevo Amanecer</strong>. Desde aquí podrás subir tus comprobantes, consultar tus
                pagos y conocer tu unidad habitacional</p>
        </section>

        <section class="highlight">
            <div>
                <i class="fi fi-sr-document"></i>
                <a href="index.php?page=pagos" class="btn-hover">Subir comprobante</a>
            </div>
        </section>

        <section class="strip">
            <h2>Últimos comprobantes subidos</h2>
            
            <?php if (count($ultimosComprobantes) > 0): ?>
                <div style="background-color: white; padding: 20px; border-radius: 8px; margin-top: 15px; overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #2C3E50;">
                                <th style="padding: 12px; text-align: left; background-color: #f8f9fa;">Tipo</th>
                                <th style="padding: 12px; text-align: left; background-color: #f8f9fa;">Estado</th>
                                <th style="padding: 12px; text-align: left; background-color: #f8f9fa;">Fecha de subida</th>
                                <th style="padding: 12px; text-align: left; background-color: #f8f9fa;">Archivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimosComprobantes as $comprobante): ?>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px;">
                                        <strong><?php echo ucfirst(htmlspecialchars($comprobante['tipo'])); ?></strong>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php 
                                            $estado = $comprobante['estado'];
                                            $colorEstado = '';
                                            $iconoEstado = '';
                                            
                                            if ($estado === 'aprobado') {
                                                $colorEstado = 'color: #27AE60; font-weight: bold;';
                                                $iconoEstado = '';
                                            } elseif ($estado === 'pendiente') {
                                                $colorEstado = 'color: #F39C12; font-weight: bold;';
                                                $iconoEstado = '';
                                            } else {
                                                $colorEstado = 'color: #C0392B; font-weight: bold;';
                                                $iconoEstado = '';
                                            }
                                        ?>
                                        <span style="<?php echo $colorEstado; ?>">
                                            <?php echo $iconoEstado . ' ' . ucfirst(htmlspecialchars($estado)); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php echo date("d/m/Y H:i", strtotime($comprobante['fecha_subida'])); ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <a href="../<?php echo htmlspecialchars($comprobante['archivo_pdf']); ?>" 
                                           target="_blank" 
                                           style="color: #3498db; text-decoration: none; font-weight: 500;">
                                            Ver PDF
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p style="margin-top: 15px; text-align: center;">
                        <a href="index.php?page=pagos" style="color: #3498db; text-decoration: none; font-weight: 600;">
                            Ver historial completo →
                        </a>
                    </p>
                </div>
            <?php else: ?>
                <p style="background-color: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #ffc107;">
                    <strong>⚠️ No tienes comprobantes subidos.</strong>
                </p>
            <?php endif; ?>
        </section>
    </main>

    <script src="assets/ham.js"></script>
</body>

</html>