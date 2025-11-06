<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Modelos/Comprobante.php';

class PagosController {
    private $db;
    private $comprobanteModel;

    public function __construct($enlace) {
        $this->db = $enlace;
        $this->comprobanteModel = new Comprobante($enlace);
    }

    public function mostrarPagos() {
        // Verificar si está logueado
        if (!isset($_SESSION['documento'])) {
            header("Location: index.php?page=login");
            exit();
        }

        $nombreUsuario = $_SESSION['nombre'];
        $documento = $_SESSION['documento'];
        $mensaje = "";

        // Procesar subida de comprobante mensual
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["comprobante"])) {
            $mensaje = $this->comprobanteModel->subirMensual($documento, $_FILES["comprobante"]);
            
            // Redirigir para evitar reenvío del formulario
            header("Location: index.php?page=pagos&uploaded=1");
            exit();
        }

        // Mostrar mensaje si viene de redirección
        if (isset($_GET['uploaded'])) {
            $mensaje = "Comprobante mensual subido exitosamente. En espera de aprobación.";
        }

        // Obtener historial de pagos mensuales
        $historial = $this->comprobanteModel->obtenerHistorialMensuales($documento);

        // Cargar la vista
        require __DIR__ . '/../Vistas/pagos.php';
    }
}
?>