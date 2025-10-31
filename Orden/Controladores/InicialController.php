<?php
require_once __DIR__ . "/../Config/database.php";
require_once __DIR__ . "/../Modelos/Comprobante.php";

class InicialController {
    private $db;
    private $comprobante;

    public function __construct($conexion) {
        $this->db = $conexion;
        $this->comprobante = new Comprobante($conexion);
    }

    public function mostrarInicial() {
        // Redirigir si no está logueado
        if (!isset($_SESSION['documento'])) {
            header("Location: index.php?page=login");
            exit();
        }

        $nombreUsuario = $_SESSION['nombre'];
        $documento = $_SESSION['documento'];
        $mensaje = ""; // Inicializar vacío

        // Procesar subida de archivo
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["comprobante"])) {
            $resultado = $this->comprobante->subirInicial($documento, $_FILES["comprobante"]);
            
            // Redirigir después de subir para evitar duplicados
            header("Location: index.php?page=inicial");
            exit();
        }

        // Obtener estado del comprobante
        $estado = $this->comprobante->obtenerEstado($documento);

        require __DIR__ . "/../Vistas/inicial.php";
    }
}
?>