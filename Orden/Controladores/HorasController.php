<?php
require_once __DIR__ . '/../Modelos/Horas.php';

class HorasController {
    private $modeloHoras;

    public function __construct($conexion) {
        $this->modeloHoras = new Horas($conexion);
    }

    /**
     * Muestra la página de registro de horas con historial
     */
    public function mostrarPagina() {
        // Verificar sesión
        if (!isset($_SESSION['documento'])) {
            header("Location: index.php?page=login");
            exit();
        }

        $documento = $_SESSION['documento'];
        $nombreUsuario = $_SESSION['nombre'];
        $mensaje = "";

        // Obtener historial de horas y comprobantes
        $historialHoras = $this->modeloHoras->obtenerHistorialHoras($documento);
        $historialCompensatorios = $this->modeloHoras->obtenerHistorialCompensatorios($documento);
        $totalHoras = $this->modeloHoras->obtenerTotalHoras($documento);

        // Cargar vista
        require_once __DIR__ . '/../Vistas/horas.php';
    }

    /**
     * Procesa el registro de horas y comprobante compensatorio
     */
    public function registrarHoras() {
        // Verificar sesión
        if (!isset($_SESSION['documento'])) {
            header("Location: index.php?page=login");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: index.php?page=horas");
            exit();
        }

        $documento = $_SESSION['documento'];
        $horas = (int) $_POST["horas"];
        $motivo = isset($_POST["motivo"]) && trim($_POST["motivo"]) !== "" ? $_POST["motivo"] : null;
        $mensaje = "";

        // Registrar horas trabajadas
        if ($this->modeloHoras->registrarHoras($documento, $horas, $motivo)) {
            // Si no completó las 21 horas → puede subir comprobante compensatorio
            if ($horas < 21 && isset($_FILES["compensatorio"]) && $_FILES["compensatorio"]["error"] == 0) {
                $resultado = $this->subirComprobanteCompensatorio($documento, $_FILES["compensatorio"]);
                $mensaje = $resultado['mensaje'];
            } else {
                $mensaje = "success|✅ Horas registradas correctamente.";
            }
        } else {
            $mensaje = "error|⚠️ Error al registrar las horas. Intenta nuevamente.";
        }

        // Redirigir con mensaje
        $_SESSION['mensaje_horas'] = $mensaje;
        header("Location: index.php?page=horas");
        exit();
    }

    /**
     * Sube el comprobante compensatorio en formato PDF
     */
    private function subirComprobanteCompensatorio($documento, $archivo) {
        $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));

        if ($ext !== "pdf") {
            return ['mensaje' => 'error|⚠️ El comprobante compensatorio debe ser un PDF.'];
        }

        // Crear carpeta si no existe
        $carpeta = __DIR__ . "/../../uploads/compensatorio/";
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        // Generar nombre único
        $nombreArchivo = $documento . "_comp_" . time() . ".pdf";
        $rutaCompleta = $carpeta . $nombreArchivo;
        $rutaBD = "uploads/compensatorio/" . $nombreArchivo;

        // Mover archivo
        if (move_uploaded_file($archivo["tmp_name"], $rutaCompleta)) {
            // Guardar en BD
            if ($this->modeloHoras->registrarComprobanteCompensatorio($documento, $rutaBD)) {
                return ['mensaje' => 'success|✅ Horas y comprobante compensatorio registrados con éxito.'];
            } else {
                return ['mensaje' => 'error|⚠️ Error al guardar el comprobante en la base de datos.'];
            }
        } else {
            return ['mensaje' => 'error|⚠️ Error al subir el comprobante compensatorio.'];
        }
    }
}