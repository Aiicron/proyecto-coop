<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Backoffice | Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/back.css" />
</head>

<body>
    <nav class="topbar">
        <div class="logo-area">
            <img src="assets/logonuevo.png" alt="Logo Cooperativa" class="logo-backoffice" />
            <h2>Administración</h2>
        </div>
        <ul class="menu">
            <li><a href="#usuarios">Usuarios</a></li>
            <li><a href="#pagos">Pagos</a></li>
            <li><a href="#horas">Horas</a></li>
            <li><a href="#unidades">Unidades</a></li>
        </ul>
    </nav>

    <main class="dashboard">
        <div class="panel-header">
            <h1>Panel de Gestión</h1>
        </div>

        <div class="grid">

            <!-- 🔹 Gestión de Usuarios -->
            <section id="usuarios" class="card">
                <h2>Gestión de Usuarios</h2>
                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Documento</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario['documento']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                                    <td><?php echo ucfirst($usuario['estado'] ?? 'Sin estado'); ?></td>
                                    <td>
                                        <form action="index.php?accion=cambiar_estado_usuario" method="POST" style="display:inline;">
                                            <input type="hidden" name="documento" value="<?php echo htmlspecialchars($usuario['documento']); ?>">
                                            <button type="submit" name="accion" value="aceptado" class="btn-aceptar">Aceptar</button>
                                            <button type="submit" name="accion" value="rechazado" class="btn-rechazar">Rechazar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- 🔹 Validación de Comprobantes -->
            <section id="pagos" class="card">
                <h2>Validación de Comprobantes</h2>
                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Documento</th>
                                <th>Usuario</th>
                                <th>Tipo</th>
                                <th>Archivo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comprobantes as $comprobante): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($comprobante['id_comprobante']); ?></td>
                                    <td><?php echo htmlspecialchars($comprobante['documento']); ?></td>
                                    <td><?php echo htmlspecialchars($comprobante['nombre']); ?></td>
                                    <td><?php echo ucfirst($comprobante['tipo']); ?></td>
                                    <td>
                                        <a href="../<?php echo htmlspecialchars($comprobante['archivo_pdf']); ?>" target="_blank">Ver PDF</a>
                                    </td>
                                    <td><?php echo ucfirst($comprobante['estado']); ?></td>
                                    <td>
                                        <form action="index.php?accion=gestionar_comprobante" method="POST" style="display:inline;">
                                            <input type="hidden" name="documento" value="<?php echo htmlspecialchars($comprobante['documento']); ?>">
                                            <input type="hidden" name="id_comprobante" value="<?php echo htmlspecialchars($comprobante['id_comprobante']); ?>">
                                            <button type="submit" name="accion" value="aprobar_comprobante" class="btn-aceptar">Aprobar</button>
                                            <button type="submit" name="accion" value="rechazar_comprobante" class="btn-rechazar">Rechazar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="horas" class="card">
    <h2>Registro de Horas Trabajadas</h2>
    <div class="table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Documento</th>
                    <th>Usuario</th>
                    <th>Horas</th>
                    <th>Motivo</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($horasRegistradas) && count($horasRegistradas) > 0): ?>
                    <?php foreach ($horasRegistradas as $hora): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($hora['id_hora']); ?></td>
                            <td><?php echo htmlspecialchars($hora['documento']); ?></td>
                            <td><?php echo htmlspecialchars($hora['nombre']); ?></td>
                            <td>
                                <strong style="color: <?php echo $hora['horas'] >= 21 ? '#7A9D54' : '#D4A574'; ?>;">
                                    <?php echo htmlspecialchars($hora['horas']); ?> hs
                                </strong>
                            </td>
                            <td><?php echo $hora['motivo'] ? htmlspecialchars($hora['motivo']) : '-'; ?></td>
                            <td><?php echo date("d/m/Y", strtotime($hora['fecha'])); ?></td>
                            <td>
                                <form action="index.php?accion=eliminar_hora" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_hora" value="<?php echo htmlspecialchars($hora['id_hora']); ?>">
                                    <button type="submit" class="btn-rechazar" 
                                            onclick="return confirm('¿Estás seguro de eliminar este registro?')">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: #D4C4B0;">
                            No hay horas registradas todavía.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Estadísticas -->
    <?php if (isset($horasRegistradas) && count($horasRegistradas) > 0): ?>
        <div style="margin-top: 20px; padding: 15px; background-color: rgba(255,255,255,0.1); border-radius: 8px;">
            <?php
            $totalRegistros = count($horasRegistradas);
            $usuariosCompletos = 0;
            $usuariosIncompletos = 0;
            
            foreach ($horasRegistradas as $h) {
                if ($h['horas'] >= 21) {
                    $usuariosCompletos++;
                } else {
                    $usuariosIncompletos++;
                }
            }
            ?>
            <p style="margin: 5px 0;"><strong>Total de registros:</strong> <?php echo $totalRegistros; ?></p>
            <p style="margin: 5px 0; color: #7A9D54;"><strong>Cumplieron 21+ horas:</strong> <?php echo $usuariosCompletos; ?></p>
            <p style="margin: 5px 0; color: #D4A574;"><strong>No cumplieron 21 horas:</strong> <?php echo $usuariosIncompletos; ?></p>
        </div>
    <?php endif; ?>
</section>

            <section id="unidades" class="card">
    <h2>Gestión de Unidades Habitacionales</h2>
    
    <!-- Formulario para asignar unidad manualmente -->
    <?php if (count($usuariosSinUnidad) > 0): ?>
        <div style="background-color: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="margin-top: 0; color: #D8F3DC;">Asignar Unidad Manualmente</h3>
            <form method="POST" action="index.php?accion=asignar_unidad" style="display: flex; gap: 10px; align-items: end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 5px; color: #FFFFFF;">Unidad:</label>
                    <input type="text" name="num_puerta" placeholder="Ej: 101" required 
                           style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #D4C4B0;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 5px; color: #FFFFFF;">Usuario:</label>
                    <select name="documento" required 
                            style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #D4C4B0;">
                        <option value="">Seleccionar usuario...</option>
                        <?php foreach ($usuariosSinUnidad as $usr): ?>
                            <option value="<?php echo htmlspecialchars($usr['documento']); ?>">
                                <?php echo htmlspecialchars($usr['nombre']) . ' (' . $usr['documento'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-aceptar" style="margin: 0;">Asignar</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Tabla de unidades -->
    <div class="table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>N° Unidad</th>
                    <th>Estado</th>
                    <th>Asignada a</th>
                    <th>Documento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($unidades) > 0): ?>
                    <?php foreach ($unidades as $unidad): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($unidad['num_puerta']); ?></strong></td>
                            <td>
                                <?php if ($unidad['documento']): ?>
                                    <span style="color: #B85C5C; font-weight: 600;">● Ocupada</span>
                                <?php else: ?>
                                    <span style="color: #7A9D54; font-weight: 600;">● Disponible</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $unidad['nombre'] ? htmlspecialchars($unidad['nombre']) : '-'; ?></td>
                            <td><?php echo $unidad['documento'] ? htmlspecialchars($unidad['documento']) : '-'; ?></td>
                            <td>
                                <?php if ($unidad['documento']): ?>
                                    <form action="index.php?accion=desasignar_unidad" method="POST" style="display:inline;">
                                        <input type="hidden" name="num_puerta" value="<?php echo htmlspecialchars($unidad['num_puerta']); ?>">
                                        <button type="submit" class="btn-rechazar" 
                                                onclick="return confirm('¿Desasignar esta unidad?')">
                                            Desasignar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #D4C4B0;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #D4C4B0;">
                            No hay unidades registradas. Crea unidades en la base de datos.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Estadísticas -->
    <?php if (count($unidades) > 0): ?>
        <div style="margin-top: 20px; padding: 15px; background-color: rgba(255,255,255,0.1); border-radius: 8px;">
            <?php
            $totalUnidades = count($unidades);
            $ocupadas = 0;
            $disponibles = 0;
            
            foreach ($unidades as $u) {
                if ($u['documento']) {
                    $ocupadas++;
                } else {
                    $disponibles++;
                }
            }
            ?>
            <p style="margin: 5px 0;"><strong>Total de unidades:</strong> <?php echo $totalUnidades; ?></p>
            <p style="margin: 5px 0; color: #B85C5C;"><strong>● Ocupadas:</strong> <?php echo $ocupadas; ?></p>
            <p style="margin: 5px 0; color: #7A9D54;"><strong>● Disponibles:</strong> <?php echo $disponibles; ?></p>
        </div>
    <?php endif; ?>
</section>

        </div>
    </main>
</body>

</html>