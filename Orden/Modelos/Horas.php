<?php
class Horas {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Registra horas trabajadas por un usuario
     */
    public function registrarHoras($documento, $horas, $motivo = null) {
        $stmt = $this->conexion->prepare(
            "INSERT INTO horas_trabajadas (documento, horas, fecha, motivo) 
             VALUES (?, ?, CURDATE(), ?)"
        );
        $stmt->bind_param("sis", $documento, $horas, $motivo);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Obtiene el historial de horas de un usuario
     */
    public function obtenerHistorialHoras($documento) {
        $stmt = $this->conexion->prepare(
            "SELECT horas, fecha, motivo 
             FROM horas_trabajadas 
             WHERE documento = ? 
             ORDER BY fecha DESC"
        );
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $historial = [];
        while ($fila = $resultado->fetch_assoc()) {
            $historial[] = $fila;
        }
        
        $stmt->close();
        return $historial;
    }

    /**
     * Registra un comprobante compensatorio (cuando no se cumplen las 21 horas)
     */
    public function registrarComprobanteCompensatorio($documento, $rutaArchivo) {
        $stmt = $this->conexion->prepare(
            "INSERT INTO comprobante_pago (documento, archivo_pdf, estado, tipo, fecha_subida) 
             VALUES (?, ?, 'pendiente', 'compensatorio', NOW())"
        );
        $stmt->bind_param("ss", $documento, $rutaArchivo);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    /**
     * Obtiene el historial de comprobantes compensatorios de un usuario
     */
    public function obtenerHistorialCompensatorios($documento) {
        $stmt = $this->conexion->prepare(
            "SELECT archivo_pdf, estado, fecha_subida 
             FROM comprobante_pago 
             WHERE documento = ? AND tipo = 'compensatorio' 
             ORDER BY fecha_subida DESC"
        );
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $historial = [];
        while ($fila = $resultado->fetch_assoc()) {
            $historial[] = $fila;
        }
        
        $stmt->close();
        return $historial;
    }

    /**
     * Calcula el total de horas trabajadas por un usuario
     */
    public function obtenerTotalHoras($documento) {
        $stmt = $this->conexion->prepare(
            "SELECT SUM(horas) as total 
             FROM horas_trabajadas 
             WHERE documento = ?"
        );
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();
        
        return $fila['total'] ?? 0;
    }

    /**
     * Obtiene todas las horas registradas con datos del usuario
     * Para el backoffice
     */
    public function obtenerTodasConUsuario() {
        $query = "SELECT h.id_hora, h.documento, u.nombre, h.horas, h.motivo, h.fecha
                  FROM horas_trabajadas h
                  INNER JOIN usuarios u ON h.documento = u.documento
                  ORDER BY h.fecha DESC";
        $resultado = mysqli_query($this->conexion, $query);
        
        $horas = [];
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $horas[] = $row;
            }
        }
        return $horas;
    }

    /**
     * Eliminar un registro de horas
     * Para correcciones del admin
     */
    public function eliminar($id_hora) {
        $stmt = $this->conexion->prepare("DELETE FROM horas_trabajadas WHERE id_hora = ?");
        $stmt->bind_param("i", $id_hora);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }
}
?>