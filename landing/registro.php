<?php
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "viviendas";
$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);
$mensaje = "";

if (isset($_POST['registro'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $documento = $_POST['cedula'];
    $motivo = $_POST['comentario'];

    $consulta = "SELECT * FROM usuarios WHERE documento='$documento' OR correo='$correo' LIMIT 1";
    $resultado = mysqli_query($enlace, $consulta);

    if (mysqli_num_rows($resultado) > 0) {
        $mensaje = "<p class='error'> El documento o correo ya está en uso. Intente con otro.</p>";
    } else {
        $insertarDatos = "INSERT INTO usuarios (nombre, correo, contrasena, documento, motivo_ingreso) 
                          VALUES ('$nombre', '$correo', '$contrasena', '$documento', '$motivo')";

        if (mysqli_query($enlace, $insertarDatos)) {
            $mensaje = "<p class='success'> Solicitud de registro enviada con éxito. En las próximas 48 hs recibirá un correo con más información si su solicitud es aprobada.</p>";
        } else {
            $mensaje = "<p class='error'> Error en el registro: " . mysqli_error($enlace) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Cooperativa Nuevo Amanecer</title>
</head>

<body>
    <div class="background"></div>

    <header>
        <nav>
            <div class="nav-content">
                <div class="nav-left">
                    <img src="logonuevo.png" alt="Logo" class="logo1">
                    <select id="language-select">
                        <option value="es">Español</option>
                        <option value="en">English</option>
                        <option value="pt">Português</option>
                        <option value="al">Deutsch</option>
                        <option value="it">Italiano</option>
                        <option value="fr">Français</option>
                    </select>
                </div>
                <ul>
                    <li><a href="registro.php">Inicio</a></li>
                    <li><a href="nosotros.html">Nosotros</a></li>
                    <li><a href="#contacto">Contacto</a></li>
                    <li><a href="../usuarios/login.php" class="IS">Iniciar sesión</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="main">
        <img src="logonuevo.png" alt="Logo Cooperativa" class="logo">
        <h1>Nuevo Amanecer</h1>

        <section class="info-cooperativa">
            <h3>¿Qué es una cooperativa de viviendas?</h3>
            <p>Una cooperativa de viviendas es una organización donde cada integrante participa activamente en la
                construcción y gestión de su hogar.</p>
            <h3>¿Por qué unirte a nosotros?</h3>
            <ul>
                <li>✔ Construís tu casa junto a tu comunidad.</li>
                <li>✔ Participás democráticamente en decisiones.</li>
                <li>✔ Accedés a un sistema justo y solidario.</li>
            </ul>
            <img src="trabajando.jpg" alt="Cooperativa trabajando" class="info-img">
        </section>

        <form id="formulario" method="POST" action="registro.php">
            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="correo" required>

            <label for="contraseña">Contraseña</label>
            <input type="password" id="contraseña" name="contrasena" required>

            <label for="cedula">Documento</label>
            <input type="number" id="cedula" name="cedula" required>

            <label for="comentario">Motivo de ingreso</label>
            <textarea id="comentario" name="comentario" rows="3"></textarea>

            <button type="submit" name="registro">Enviar solicitud</button>
            <div id="mensaje" class="mensaje"><?php echo $mensaje; ?></div>
        </form>
    </main>

    <footer>
        <div class="footer-columns">
            <div class="footer-column">
                <h4>Navegación</h4>
                <a href="registro.php">Inicio</a>
                <a href="nosotros.html">Nosotros</a>
                <a href="../usuarios/login.php">Iniciar Sesión</a>
            </div>

            <div class="footer-column social-center">
                <h4>Redes Sociales</h4>
                <div class="social-icons">
                    <a href="https://www.facebook.com/profile.php?id=61578779908016" target="_blank"><img
                            src="facebook.png" alt="Facebook" class="logos"></a>
                    <a href="https://x.com/NewAmanecer" target="_blank"><img src="X.png" alt="Twitter/X"
                            class="logos"></a>
                    <a href="https://www.instagram.com/nuevo_amanecer220507/" target="_blank"><img src="instagram.png"
                            alt="Instagram" class="logos"></a>
                </div>
            </div>

            <div class="footer-column" id="contacto">
                <h4>Contacto</h4>
                <p>NuevoAmanecer220507@hotmail.com</p>
                <p>+598 92 052 018</p>
                <p>Montevideo, Uruguay</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 TAT Software Group. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>

</html>