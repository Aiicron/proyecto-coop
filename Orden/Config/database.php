<?php
$servidor = "localhost";
$usuario = "root";
$clave = "";
$baseDeDatos = "vivienda";

$enlace = mysqli_connect($servidor, $usuario, $clave, $baseDeDatos);

if (!$enlace) {
    die(" Error al conectar con la base de datos: " . mysqli_connect_error());
}

mysqli_set_charset($enlace, "utf8");

return $enlace;
?>
