<?php
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "viviendas";
$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);
$mensajeExito = false;

if (isset($_POST['registro'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];
    $telefono = $_POST['telefono'];
    $cedula = $_POST['cedula'];
    $comentario = $_POST['comentario'];

    $insertarDatos = "INSERT INTO usuarios (nombre, email, contraseña, telefono, cedula, comentario) 
                      VALUES ('$nombre', '$email', '$contraseña', '$telefono', '$cedula', '$comentario')";

    $ejecutarInsertar = mysqli_query($enlace, $insertarDatos);

    if ($ejecutarInsertar) {
        $mensajeExito = true;
    }
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>

<body>
    <form id="formulario">
        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" required>

        <label for="email">Correo electrónico</label>
        <input type="email" id="email" required>

        <label for="contraseña">Contraseña</label>
        <input type="password" id="contraseña" required>

        <label for="telefono">Teléfono</label>
        <input type="number" id="telefono" required>

        <label for="cedula">Documento</label>
        <input type="number" id="cedula" required>

        <label for="comentario">Motivo de ingreso</label>
        <textarea id="comentario" rows="3"></textarea>

        <button type="submit">Enviar solicitud</button>
        <div id="mensaje" class="mensaje"></div>
    </form>
</body>
<style>
    form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 2rem;
    }

    label {
        font-weight: bold;
        text-align: left;
    }

    input,
    textarea {
        padding: 0.6rem;
        border: 1px solid #c3b091;
        border-radius: 4px;
        font-size: 1rem;
    }

    button {
        background-color: #8b6f47;
        color: white;
        border: none;
        padding: 0.8rem;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #6e5432;
    }

    .mensaje {
        margin-top: 1rem;
        font-weight: bold;
    }

    .success {
        color: green;
    }

    .error {
        color: red;
    }
</style>

</html>