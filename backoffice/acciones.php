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

// --- 🔹 Gestión de Comprobantes Iniciales ---
    if ($accion === "aprobar_comprobante" || $accion === "rechazar_comprobante") {
        $id_comprobante = mysqli_real_escape_string($enlace, $_POST['id_comprobante']);

        if ($accion === "aprobar_comprobante") {
            // 1. Aprobar comprobante
            mysqli_query($enlace, "UPDATE comprobante_pago SET estado='aprobado' WHERE id_comprobante='$id_comprobante'");

            // 2. Buscar una unidad disponible
            $unidadRes = mysqli_query($enlace, "SELECT num_puerta FROM unidades_habitacionales 
                                                WHERE num_puerta NOT IN (SELECT num_puerta FROM asigna) 
                                                LIMIT 1");

            if ($unidadRes && mysqli_num_rows($unidadRes) > 0) {
                $unidad = mysqli_fetch_assoc($unidadRes)['num_puerta'];

                // 3. Asignar unidad al usuario
                mysqli_query($enlace, "INSERT INTO asigna (num_puerta, documento) VALUES ('$unidad', '$doc')");
            }
        } elseif ($accion === "rechazar_comprobante") {
            // Rechazar comprobante
            mysqli_query($enlace, "UPDATE comprobante_pago SET estado='rechazado' WHERE id_comprobante='$id_comprobante'");
        }
    }


header("Location: backoffice.php");
exit();