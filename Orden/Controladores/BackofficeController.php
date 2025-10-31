<?php

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Modelos/Usuario.php';
require_once __DIR__ . '/../Modelos/Comprobante.php';
require_once __DIR__ . '/../Modelos/Unidad.php';
require_once __DIR__ . '/../Modelos/Horas.php'; // ⬅️ CAMBIO: Horas en plural

class BackofficeController {
    private $db;
    private $usuarioModel;
    private $comprobanteModel;
    private $unidadModel;
    private $horasModel; // ⬅️ CAMBIO: horasModel

    public function __construct($enlace) {
        $this->db = $enlace;
        $this->usuarioModel = new Usuario($enlace);
        $this->comprobanteModel = new Comprobante($enlace);
        $this->unidadModel = new Unidad($enlace);
        $this->horasModel = new Horas($enlace); // ⬅️ CAMBIO: Horas
    }

    // Mostrar el panel de backoffice
    public function mostrarBackoffice() {
    // Obtener datos para la vista
    $usuarios = $this->usuarioModel->obtenerTodosConEstado();
    $comprobantes = $this->comprobanteModel->obtenerTodosConUsuario();
    $horasRegistradas = $this->horasModel->obtenerTodasConUsuario();
    $unidades = $this->unidadModel->obtenerTodasConEstado(); // ⬅️ AGREGAR
    $usuariosSinUnidad = $this->unidadModel->obtenerUsuariosSinUnidad(); // ⬅️ AGREGAR
    
    // Cargar la vista
    require __DIR__ . '/../Vistas/backoffice.php';
}

    // Cambiar estado de un usuario (aceptar/rechazar)
    public function cambiarEstadoUsuario() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $documento = $_POST['documento'] ?? '';
            $nuevoEstado = $_POST['accion'] ?? '';

            if ($documento && $nuevoEstado) {
                $this->usuarioModel->cambiarEstadoAutenticacion($documento, $nuevoEstado);
            }

            header("Location: index.php?page=backoffice");
            exit();
        }
    }

    // Gestionar comprobantes (aprobar/rechazar)
    public function gestionarComprobante() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_comprobante = $_POST['id_comprobante'] ?? '';
            $accion = $_POST['accion'] ?? '';
            $documento = $_POST['documento'] ?? '';

            if ($accion === "aprobar_comprobante" && $id_comprobante) {
                $this->comprobanteModel->aprobar($id_comprobante);
                $unidadDisponible = $this->unidadModel->obtenerDisponible();

                if ($unidadDisponible && $documento) {
                    $this->unidadModel->asignar($unidadDisponible, $documento);
                }

            } elseif ($accion === "rechazar_comprobante" && $id_comprobante) {
                $this->comprobanteModel->rechazar($id_comprobante);
            }

            header("Location: index.php?page=backoffice");
            exit();
        }
    }

    // Eliminar registro de horas
    public function eliminarHora() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_hora = $_POST['id_hora'] ?? '';

            if ($id_hora) {
                $this->horasModel->eliminar($id_hora); // ⬅️ CAMBIO
            }

            header("Location: index.php?page=backoffice#horas");
            exit();
        }
    }

    // Asignar unidad manualmente
    public function asignarUnidad() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $num_puerta = $_POST['num_puerta'] ?? '';
            $documento = $_POST['documento'] ?? '';

            if ($num_puerta && $documento) {
                // Verificar que la unidad no esté asignada
                if (!$this->unidadModel->estaAsignada($num_puerta)) {
                    $this->unidadModel->asignar($num_puerta, $documento);
                }
            }

            header("Location: index.php?page=backoffice#unidades");
            exit();
        }
    }

    // Desasignar unidad
    public function desasignarUnidad() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $num_puerta = $_POST['num_puerta'] ?? '';

            if ($num_puerta) {
                $this->unidadModel->desasignar($num_puerta);
            }

            header("Location: index.php?page=backoffice#unidades");
            exit();
        }
    }
}
?>