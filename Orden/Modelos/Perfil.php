<?php
class Perfil {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene toda la información del perfil del usuario
     */
    public function obtenerInformacionCompleta($documento) {
        $stmt = $this->conexion->prepare(
            "SELECT u.documento, u.nombre, u.correo, u.motivo_ingreso,
                    ra.estado as estado_autenticacion
             FROM usuarios u
             LEFT JOIN registro_autenticacion ra ON u.documento = ra.documento
             WHERE u.documento = ?"
        );
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $perfil = $resultado->fetch_assoc();
        $stmt->close();
        
        return $perfil;
    }

    /**
     * Obtiene el número de vivienda asignada al usuario
     */
    public function obtenerViviendaAsignada($documento) {
        $stmt = $this->conexion->prepare(
            "SELECT uh.num_puerta, uh.direccion
             FROM asigna a
             INNER JOIN unidades_habitacionales uh ON a.num_puerta = uh.num_puerta
             WHERE a.documento = ?
             LIMIT 1"
        );
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $vivienda = $resultado->fetch_assoc();
        $stmt->close();
        
        return $vivienda;
    }

    /**
     * Obtiene el total de horas trabajadas por el usuario
     */
    public function obtenerTotalHoras($documento) {
        $stmt = $this->conexion->prepare(
            "SELECT COALESCE(SUM(horas), 0) as total_horas
             FROM horas_trabajadas
             WHERE documento = ?"
        );
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();
        
        return $fila['total_horas'];
    }

    /**
     * Obtiene estadísticas de comprobantes del usuario
     */
    public function obtenerEstadisticasComprobantes($documento) {
        $stmt = $this->conexion->prepare(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'rechazado' THEN 1 ELSE 0 END) as rechazados
             FROM comprobante_pago
             WHERE documento = ?"
        );
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $estadisticas = $resultado->fetch_assoc();
        $stmt->close();
        
        return $estadisticas;
    }

    /**
     * Actualiza los datos personales del usuario (nombre, apellidos, email)
     */
    public function actualizarDatosPersonales($documento, $nombre, $correo) {
        // Verificar que el email no esté en uso por otro usuario
        $stmt = $this->conexion->prepare(
            "SELECT documento FROM usuarios WHERE correo = ? AND documento != ?"
        );
        $stmt->bind_param("ss", $correo, $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $stmt->close();
            return ['success' => false, 'mensaje' => 'El email ya está en uso por otro usuario.'];
        }
        $stmt->close();

        // Actualizar datos
        $stmt = $this->conexion->prepare(
            "UPDATE usuarios 
             SET nombre = ?, correo = ?
             WHERE documento = ?"
        );
        $stmt->bind_param("sssss", $nombre, $correo, $documento);
        $exito = $stmt->execute();
        $stmt->close();

        if ($exito) {
            return ['success' => true, 'mensaje' => 'Datos actualizados correctamente.'];
        } else {
            return ['success' => false, 'mensaje' => 'Error al actualizar los datos.'];
        }
    }

    /**
     * Verifica si la contraseña actual es correcta
     */
    public function verificarContrasenaActual($documento, $contrasenaActual) {
        $stmt = $this->conexion->prepare(
            "SELECT contrasena FROM usuarios WHERE documento = ?"
        );
        $stmt->bind_param("s", $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();

        if ($usuario && password_verify($contrasenaActual, $usuario['contrasena'])) {
            return true;
        }
        return false;
    }

}