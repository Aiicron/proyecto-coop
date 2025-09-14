<?php
session_start();

$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "viviendas";

$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);
$mensaje = "";
$mensajeClase = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = mysqli_real_escape_string($enlace, $_POST['correo']);
    $contrasena = mysqli_real_escape_string($enlace, $_POST['contrasena']);

    // Buscar al usuario
    $consulta = "SELECT * FROM usuarios WHERE correo='$correo' LIMIT 1";
    $resultado = mysqli_query($enlace, $consulta);

    if ($resultado && mysqli_num_rows($resultado) == 1) {
        $usuario = mysqli_fetch_assoc($resultado);

        // Verificar contraseña
        if ($usuario['contrasena'] === $contrasena) {
            // Verificar estado en registro_autenticacion
            $doc = $usuario['documento'];
            $estadoQuery = "SELECT estado FROM registro_autenticacion WHERE documento='$doc' LIMIT 1";
            $estadoRes = mysqli_query($enlace, $estadoQuery);
            $estado = mysqli_fetch_assoc($estadoRes);

            if ($estado['estado'] === "aceptado") {
                $_SESSION['documento'] = $usuario['documento'];
                $_SESSION['nombre'] = $usuario['nombre'];
                header("Location: ../frontend-coop/bienvenida.html");
                exit();
            } elseif ($estado['estado'] === "pendiente") {
                $mensaje = "Tu solicitud aún está pendiente de aprobación.";
                $mensajeClase = "error";
            } elseif ($estado['estado'] === "rechazado") {
                $mensaje = "Tu solicitud fue rechazada. Contacta a la administración.";
                $mensajeClase = "error";
            }
        } else {
            $mensaje = "Contraseña incorrecta.";
            $mensajeClase = "error";
        }
    } else {
        $mensaje = "Usuario no encontrado.";
        $mensajeClase = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Cooperativa</title>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Montserrat', sans-serif;
            background: #d4d3d3;
            text-align: center;
        }

        .background {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url(fondo2.jpg) no-repeat center;
            background-size: cover;
            filter: blur(10px);
            z-index: -1;
        }

        .login-main {
            width: 100%;
            max-width: 400px;
            background: #fffdf6;
            padding: 3rem 2rem;
            border-radius: 8px;
            position: relative;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .logo {
            width: 70px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: block;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .logo:hover {
            transform: scale(1.1);
        }

        h2 {
            font-family: 'Exo 2', sans-serif;
            margin-bottom: 2rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            text-align: left;
        }

        label {
            font-weight: bold;
        }

        input {
            padding: 0.6rem;
            border: 1px solid #c3b091;
            border-radius: 4px;
            font-size: 1rem;
        }

        button {
            background-color: #8b6f47;
            color: #fffdf6;
            border: none;
            padding: 0.8rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #6e5432;
        }

        .mensaje-container {
            margin-top: 1rem;
            text-align: center;
        }

        .mensaje {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 6px;
            font-weight: bold;
        }

        .mensaje.error {
            background-color: #c0392b;
            color: #fff;
        }

        .mensaje.success {
            background-color: #27ae60;
            color: #fff;
        }

        form a {
            text-align: center;
            display: block;
            margin-top: 1rem;
            color: #8b6f47;
            text-decoration: none;
        }

        form a:hover {
            text-decoration: underline;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="background"></div>

    <main class="login-main">
        <img src="logonuevo.png" alt="Logo Cooperativa" class="logo">
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="login.php">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="correo" placeholder="Tu correo" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="contrasena" placeholder="Tu contraseña" required>

            <button type="submit">Ingresar</button>

            <!-- Mensaje centrado -->
            <?php if (!empty($mensaje)) : ?>
                <div class="mensaje-container">
                    <div class="mensaje <?php echo $mensajeClase; ?>">
                        <?php echo $mensaje; ?>
                    </div>
                </div>
            <?php endif; ?>

            <a href="../landing/registro.php">Volver</a>
        </form>
    </main>

    <footer>&copy; 2025 TAT Software Group. Todos los derechos reservados.</footer>
</body>

</html>
