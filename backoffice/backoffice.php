<?php


$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "viviendas";

$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

// Traer todos los usuarios con estado
$query = "SELECT u.documento, u.nombre, u.correo, r.estado
          FROM usuarios u
          LEFT JOIN registro_autenticacion r ON u.documento = r.documento";
$resultado = mysqli_query($enlace, $query);


// Consulta de comprobantes iniciales pendientes
$queryPagos = "SELECT c.id_comprobante, c.documento, u.nombre, c.archivo_pdf, c.estado, c.tipo
               FROM comprobante_pago c
               INNER JOIN usuarios u ON c.documento = u.documento
               ORDER BY c.id_comprobante DESC";
$resultPagos = mysqli_query($enlace, $queryPagos);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Backoffice | Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <nav class="topbar">
        <div class="logo-area">
            <img src="logoredondo.png" alt="Logo Cooperativa" class="logo-backoffice" />
            <h2>Administración</h2>
        </div>
        <ul class="menu">
            <li><a href="#usuarios">Usuarios</a></li>
            <li><a href="#pagos">Pagos</a></li>
            <li><a href="#horas">Horas</a></li>
            <li><a href="#unidades">Unidades</a></li>
            <li><a href="#reportes">Reportes</a></li>
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
                        <?php while ($row = mysqli_fetch_assoc($resultado)) : ?>
                            <tr>
                                <td><?php echo $row['documento']; ?></td>
                                <td><?php echo $row['nombre']; ?></td>
                                <td><?php echo $row['correo']; ?></td>
                                <td><?php echo ucfirst($row['estado']); ?></td>
                                <td>
                                    <form action="acciones.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="documento" value="<?php echo $row['documento']; ?>">
                                        <button type="submit" name="accion" value="aceptado" class="btn-aceptar">Aceptar</button>
                                        <button type="submit" name="accion" value="rechazado" class="btn-rechazar">Rechazar</button>
                                        <button type="submit" name="accion" value="pendiente" class="btn-actualizar">Pendiente</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- 🔹 Los demás módulos (pagos, horas, unidades, reportes) -->
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
            <?php while ($row = mysqli_fetch_assoc($resultPagos)) : ?>
                <tr>
                    <td><?php echo $row['id_comprobante']; ?></td>
                    <td><?php echo $row['documento']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo ucfirst($row['tipo']); ?></td>
                    <td>
                        <a href="../<?php echo $row['archivo_pdf']; ?>" target="_blank">Ver PDF</a>
                    </td>
                    <td><?php echo ucfirst($row['estado']); ?></td>
                    <td>
                        <form action="acciones.php" method="POST" style="display:inline;">
                            <input type="hidden" name="documento" value="<?php echo $row['documento']; ?>">
                            <input type="hidden" name="id_comprobante" value="<?php echo $row['id_comprobante']; ?>">
                            <button type="submit" name="accion" value="aprobar_comprobante" class="btn-aceptar">Aprobar</button>
                            <button type="submit" name="accion" value="rechazar_comprobante" class="btn-rechazar">Rechazar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

            <section id="horas" class="card">
                <h2>Registro de Horas</h2>
                <p>Aquí irá la lógica de horas trabajadas.</p>
            </section>

            <section id="unidades" class="card">
                <h2>Asignación de Unidades</h2>
                <p>Aquí irá la lógica de unidades habitacionales.</p>
            </section>

            <section id="reportes" class="card">
                <h2>Estado General</h2>
                <p>Aquí se mostrarán los reportes generales.</p>
            </section>
        </div>
    </main>
</body>
</html>
