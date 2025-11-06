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
     * Actualiza los datos personales del usuario (nombre y correo)
     */
    public function actualizarDatosPersonales($documento, $nombre, $correo) {
        // Verificar que el correo no esté en uso por otro usuario
        $stmt = $this->conexion->prepare(
            "SELECT documento FROM usuarios WHERE correo = ? AND documento != ?"
        );
        $stmt->bind_param("ss", $correo, $documento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $stmt->close();
            return ['success' => false, 'mensaje' => 'El correo ya está en uso por otro usuario.'];
        }
        $stmt->close();

        // Actualizar datos
        $stmt = $this->conexion->prepare(
            "UPDATE usuarios 
             SET nombre = ?, correo = ?
             WHERE documento = ?"
        );
        $stmt->bind_param("sss", $nombre, $correo, $documento);
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

    /**
     * Cambia la contraseña del usuario
     */
    public function cambiarContrasena($documento, $contrasenaActual, $contrasenaNueva) {
        // Verificar contraseña actual
        if (!$this->verificarContrasenaActual($documento, $contrasenaActual)) {
            return ['success' => false, 'mensaje' => 'La contraseña actual es incorrecta.'];
        }

        // Validar que la nueva contraseña tenga al menos 6 caracteres
        if (strlen($contrasenaNueva) < 6) {
            return ['success' => false, 'mensaje' => 'La nueva contraseña debe tener al menos 6 caracteres.'];
        }

        // Hash de la nueva contraseña
        $hashNuevo = password_hash($contrasenaNueva, PASSWORD_DEFAULT);

        // Actualizar contraseña
        $stmt = $this->conexion->prepare(
            "UPDATE usuarios SET contrasena = ? WHERE documento = ?"
        );
        $stmt->bind_param("ss", $hashNuevo, $documento);
        $exito = $stmt->execute();
        $stmt->close();

        if ($exito) {
            return ['success' => true, 'mensaje' => 'Contraseña actualizada correctamente.'];
        } else {
            return ['success' => false, 'mensaje' => 'Error al actualizar la contraseña.'];
        }
    }
}