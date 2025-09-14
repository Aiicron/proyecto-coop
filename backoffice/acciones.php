<?php

$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "viviendas";

$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doc = mysqli_real_escape_string($enlace, $_POST['documento']);
    $accion = mysqli_real_escape_string($enlace, $_POST['accion']);

    // Verificar si existe registro
    $check = mysqli_query($enlace, "SELECT * FROM registro_autenticacion WHERE documento='$doc'");
    if (mysqli_num_rows($check) > 0) {
        $query = "UPDATE registro_autenticacion SET estado='$accion' WHERE documento='$doc'";
    } else {
        $query = "INSERT INTO registro_autenticacion (documento, estado) VALUES ('$doc','$accion')";
    }
    mysqli_query($enlace, $query);
}

header("Location: backoffice.php");
exit();