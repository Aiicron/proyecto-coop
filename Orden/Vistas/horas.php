<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Horas - Cooperativa Nuevo Amanecer</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/front.css">
    <style>
        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }
        .tabla th, .tabla td {
            border: 1px solid #D4C4B0;
            padding: 12px;
            text-align: center;
        }
        .tabla th {
            background-color: #8B7355;
            color: white;
            font-weight: 600;
        }
        .tabla tr:nth-child(even) {
            background-color: #F5F1E8;
        }
        .tabla tr:hover {
            background-color: #E8DCC4;
        }
        .pendiente { 
            color: #D4A574; 
            font-weight: 600;
        }
        .aprobado { 
            color: #7A9D54; 
            font-weight: 600;
        }
        .rechazado { 
            color: #B85C5C; 
            font-weight: 600;
        }
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #7A9D54;
        }
        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #B85C5C;
        }
        .info-box {
            background-color: #F5F1E8;
            border-left: 4px solid #8B7355;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #5D4E37;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #5D4E37;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #D4C4B0;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Montserrat', sans-serif;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #8B7355;
        }
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #8B7355;
            font-size: 12px;
        }
        .btn-primary {
            display: inline-block;
            margin-top: 1rem;
            padding: 12px 24px;
            background: #8B7355;
            color: #FFFFFF;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
            font-size: 16px;
        }
        .btn-primary:hover {
            background: #5D4E37;
            box-shadow: 0 4px 12px rgba(93, 78, 55, 0.25);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- NAVEGACIÓN -->
    <nav>
        <div class="nav-content">
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="nav-center">
                <h2 class="nav-title">Nuevo Amanecer</h2>
            </div>
            <div class="nav-right">
                <img src="assets/logonuevo.png" class="logo1" alt="Logo cooperativa">
            </div>
        </div>
    </nav>

    <!-- SIDEBAR -->
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

    <!-- CONTENIDO PRINCIPAL -->
    <main class="dashboard">
        
        <!-- MENSAJE DE CONFIRMACIÓN -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert <?php echo $mensajeTipo === 'success' ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- INFO GENERAL -->
        <div class="info-box">
            <h3>Resumen de Horas</h3>
            <p><strong>Total de horas acumuladas:</strong> <?php echo $totalHoras; ?> horas</p>
            <p><small>Se requieren al menos 21 horas semanales de trabajo cooperativo.</small></p>
        </div>

        <!-- REGISTRAR NUEVAS HORAS -->
        <section class="strip alt">
            <h2>Registrar Nuevas Horas</h2>
            
            <form method="POST" action="index.php?accion=registrar_horas" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="horas">Cantidad de horas trabajadas: *</label>
                    <input type="number" id="horas" name="horas" min="0" max="40" step="0.5" required 
                           placeholder="Ej: 21">
                </div>

                <div class="form-group">
                    <label for="motivo">Motivo (solo si no llegaste a 21 horas):</label>
                    <textarea id="motivo" name="motivo" rows="3" 
                              placeholder="Ej: Enfermedad, compromiso familiar, etc."></textarea>
                    <small>Este campo es opcional, pero recomendado si no cumpliste las 21 horas.</small>
                </div>

                <div class="form-group">
                    <label for="compensatorio">Comprobante de pago compensatorio (PDF):</label>
                    <input type="file" id="compensatorio" name="compensatorio" accept="application/pdf">
                    <small>Solo si no cumpliste las 21 horas y realizaste un pago compensatorio.</small>
                </div>

                <button type="submit" class="btn-primary">Registrar Horas</button>
            </form>
        </section>

        <!-- HISTORIAL DE HORAS -->
        <section class="strip">
            <h2>Historial de Horas Trabajadas</h2>
            
            <?php if (count($historialHoras) > 0): ?>
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Horas</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historialHoras as $h): ?>
                            <tr>
                                <td><?php echo date("d/m/Y", strtotime($h['fecha'])); ?></td>
                                <td><strong><?php echo $h['horas']; ?> hs</strong></td>
                                <td><?php echo $h['motivo'] ? htmlspecialchars($h['motivo']) : "-"; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #8B7355; padding: 20px;">
                    No has registrado horas todavía. ¡Comienza a registrar tu trabajo cooperativo!
                </p>
            <?php endif; ?>
        </section>

        <!-- HISTORIAL DE COMPROBANTES COMPENSATORIOS -->
        <section class="strip alt">
            <h2>Comprobantes Compensatorios</h2>
            
            <?php if (count($historialCompensatorios) > 0): ?>
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Fecha de Subida</th>
                            <th>Archivo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historialCompensatorios as $comp): ?>
                            <tr>
                                <td><?php echo date("d/m/Y H:i", strtotime($comp['fecha_subida'])); ?></td>
                                <td>
                                    <a href="../<?php echo htmlspecialchars($comp['archivo_pdf']); ?>" target="_blank" 
                                       style="color: #8B7355; text-decoration: underline; font-weight: 600;">
                                        Ver comprobante
                                    </a>
                                </td>
                                <td class="<?php echo htmlspecialchars($comp['estado']); ?>">
                                    <?php 
                                    $estado = $comp['estado'];
                                    if ($estado === 'aprobado') echo '';
                                    elseif ($estado === 'pendiente') echo '';
                                    else echo '';
                                    echo ucfirst($estado); 
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #8B7355; padding: 20px;">
                    No has subido comprobantes compensatorios.
                </p>
            <?php endif; ?>
        </section>

    </main>

    <script src="assets/ham.js"></script>
</body>
</html>