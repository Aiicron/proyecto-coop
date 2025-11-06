<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Cooperativa Nuevo Amanecer</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/front.css">
    <style>
        .perfil-header {
            background: linear-gradient(135deg, #C4A77D 0%, #D4C4B0 100%);
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(93, 78, 55, 0.15);
        }
        .perfil-header h1 {
            margin: 0;
            color: #5D4E37;
            font-size: 28px;
        }
        .perfil-header p {
            margin: 5px 0 0 0;
            color: #3E3226;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: linear-gradient(135deg, #F5F1E8 0%, #E8DCC4 100%);
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #8B7355;
            box-shadow: 0 2px 8px rgba(93, 78, 55, 0.1);
        }
        .stat-card h3 {
            margin: 0 0 0.5rem 0;
            color: #5D4E37;
            font-size: 16px;
        }
        .stat-card .valor {
            font-size: 32px;
            font-weight: 600;
            color: #8B7355;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #D4C4B0;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Montserrat', sans-serif;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #8B7355;
        }
        .form-group input:disabled {
            background-color: #F5F1E8;
            color: #8B7355;
            cursor: not-allowed;
        }
        .btn-primary {
            display: inline-block;
            margin-top: 1rem;
            padding: 10px 18px;
            background: #8B7355;
            color: #FFFFFF;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
        }
        .btn-primary:hover {
            background: #5D4E37;
            box-shadow: 0 4px 12px rgba(93, 78, 55, 0.25);
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #C4A77D;
        }
        .btn-secondary:hover {
            background: #8B7355;
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
        .estado-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 5px;
        }
        .estado-badge.pendiente {
            background-color: #FFF8E1;
            color: #D4A574;
        }
        .estado-badge.aceptado {
            background-color: #E8F5E9;
            color: #7A9D54;
        }
        .estado-badge.rechazado {
            background-color: #FFEBEE;
            color: #B85C5C;
        }
        .sidebar ul li a.active {
            background-color: #E8DCC4;
            color: #5D4E37;
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

    <!-- CONTENIDO PRINCIPAL -->
    <main class="dashboard">
        
        <!-- MENSAJE DE CONFIRMACIÓN -->
        <?php 
        if (isset($_SESSION['mensaje_perfil'])) {
            $partes = explode('|', $_SESSION['mensaje_perfil']);
            $tipo = $partes[0];
            $texto = $partes[1];
            $clase = $tipo === 'success' ? 'alert-success' : 'alert-error';
            echo "<div class='alert $clase'>$texto</div>";
            unset($_SESSION['mensaje_perfil']);
        }
        ?>

        <!-- HEADER DEL PERFIL -->
        <div class="perfil-header">
            <h1><?php echo htmlspecialchars($informacion['nombre']); ?></h1>
            <p><strong>Documento:</strong> <?php echo htmlspecialchars($informacion['documento']); ?></p>
            <span class="estado-badge <?php echo $informacion['estado_autenticacion']; ?>">
                <?php echo ucfirst($informacion['estado_autenticacion']); ?>
            </span>
        </div>

        <!-- ESTADÍSTICAS -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Vivienda Asignada</h3>
                <div class="valor">
                    <?php echo $vivienda ? $vivienda['num_puerta'] : '-'; ?>
                </div>
                <?php if ($vivienda): ?>
                    <small><?php echo htmlspecialchars($vivienda['direccion']); ?></small>
                <?php else: ?>
                    <small>Sin asignar</small>
                <?php endif; ?>
            </div>

            <div class="stat-card">
                <h3>Horas Trabajadas</h3>
                <div class="valor"><?php echo $totalHoras; ?></div>
                <small>Total acumuladas</small>
            </div>

            <div class="stat-card">
                <h3>Comprobantes Aprobados</h3>
                <div class="valor"><?php echo $estadisticasComprobantes['aprobados']; ?></div>
                <small>de <?php echo $estadisticasComprobantes['total']; ?> totales</small>
            </div>

            <div class="stat-card">
                <h3>Comprobantes Pendientes</h3>
                <div class="valor"><?php echo $estadisticasComprobantes['pendientes']; ?></div>
                <small>En revisión</small>
            </div>
        </div>

        <!-- EDITAR DATOS PERSONALES -->
        <section class="strip">
            <h2>Datos Personales</h2>
            
            <form method="POST" action="index.php?accion=actualizar_datos">
                <div class="form-group">
                    <label for="documento">Documento (no editable):</label>
                    <input type="text" id="documento" value="<?php echo htmlspecialchars($informacion['documento']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre Completo:</label>
                    <input type="text" id="nombre" name="nombre" 
                           value="<?php echo htmlspecialchars($informacion['nombre']); ?>" required>
                </div>


                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($informacion['correo']); ?>" required>
                </div>

                <button type="submit" class="btn-primary">Guardar Cambios</button>
            </form>
        </section>


    </main>

    <script src="assets/ham.js"></script>
</body>
</html>