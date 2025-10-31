<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Cooperativa</title>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/login.css">
</head>
<body>
    <div class="background"></div>
    <main class="login-main">
        <img src="assets/logonuevo.png" alt="Logo Cooperativa" class="logo">
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="index.php?accion=login">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="correo" placeholder="Tu correo" required>
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="contrasena" placeholder="Tu contraseña" required>
            <button type="submit">Ingresar</button>
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje-container">
                    <div class="mensaje <?php echo htmlspecialchars($mensajeClase); ?>">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                </div>
            <?php endif; ?>
            <a href="index.php">Volver</a>
        </form>
    </main>
    <footer>&copy; 2025 TAT Software Group. Todos los derechos reservados.</footer>
</body>
</html>