<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Modelos/Usuario.php';
require_once __DIR__ . '/../Modelos/Comprobante.php';

class BienvenidaController {
    private $db;
    private $usuarioModel;
    private $comprobanteModel;

    public function __construct($enlace) {
        $this->db = $enlace;
        $this->usuarioModel = new Usuario($enlace);
        $this->comprobanteModel = new Comprobante($enlace);
    }

    public function mostrarBienvenida() {
        // Verificar si está logueado
        if (!isset($_SESSION['documento'])) {
            header("Location: index.php?page=login");
            exit();
        }

        $documento = $_SESSION['documento'];
        $nombreUsuario = $_SESSION['nombre'];

        // Verificar si tiene comprobante inicial aprobado
        $tieneAcceso = $this->comprobanteModel->tieneComprobanteInicialAprobado($documento);

        if (!$tieneAcceso) {
            // Si no tiene comprobante aprobado, redirigir a inicial
            header("Location: index.php?page=inicial");
            exit();
        }

        $ultimosComprobantes = $this->comprobanteModel->obtenerUltimosComprobantes($documento, 3);

        // Cargar la vista
        require __DIR__ . '/../Vistas/bienvenida.php';
    }
}
?>