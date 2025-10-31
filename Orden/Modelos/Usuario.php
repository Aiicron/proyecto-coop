<?php
class Usuario {
    public $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // --- Métodos de registro existentes ---
    public function existeUsuario($documento, $correo) {
        $sql = "SELECT * FROM usuarios WHERE documento='$documento' OR correo='$correo' LIMIT 1";
        $resultado = mysqli_query($this->db, $sql);
        return mysqli_num_rows($resultado) > 0;
    }

    public function registrar($nombre, $correo, $contrasena, $documento, $motivo) {
        $insertarUsuario = "INSERT INTO usuarios (documento, nombre, correo, contrasena, motivo_ingreso) 
                            VALUES ('$documento', '$nombre', '$correo', '$contrasena', '$motivo')";
        return mysqli_query($this->db, $insertarUsuario);
    }

    public function registrarAutenticacion($documento) {
        $insertarRegistro = "INSERT INTO registro_autenticacion (documento, estado) 
                             VALUES ('$documento', 'pendiente')";
        return mysqli_query($this->db, $insertarRegistro);
    }

    // --- Métodos nuevos para login ---
    public function obtenerPorCorreo($correo) {
        $correo = mysqli_real_escape_string($this->db, $correo);
        $sql = "SELECT * FROM usuarios WHERE correo='$correo' LIMIT 1";
        $resultado = mysqli_query($this->db, $sql);
        return ($resultado && mysqli_num_rows($resultado) > 0) ? mysqli_fetch_assoc($resultado) : null;
    }

    public function obtenerEstadoAutenticacion($documento) {
        $documento = mysqli_real_escape_string($this->db, $documento);
        $sql = "SELECT estado FROM registro_autenticacion WHERE documento='$documento' LIMIT 1";
        $resultado = mysqli_query($this->db, $sql);
        return ($resultado && mysqli_num_rows($resultado) > 0) ? mysqli_fetch_assoc($resultado)['estado'] : null;
    }

    public function tieneComprobanteInicialAprobado($documento) {
        $documento = mysqli_real_escape_string($this->db, $documento);
        $sql = "SELECT * FROM comprobante_pago 
                WHERE documento='$documento' AND tipo='inicial' AND estado='aprobado' LIMIT 1";
        $resultado = mysqli_query($this->db, $sql);
        return ($resultado && mysqli_num_rows($resultado) > 0);
    }

// Obtener todos los usuarios con su estado de autenticación
    public function obtenerTodosConEstado() {
        $query = "SELECT u.documento, u.nombre, u.correo, r.estado
                  FROM usuarios u
                  LEFT JOIN registro_autenticacion r ON u.documento = r.documento";
        $resultado = mysqli_query($this->db, $query);
        
        $usuarios = [];
        while ($row = mysqli_fetch_assoc($resultado)) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }

    // Cambiar estado de autenticación de un usuario
    public function cambiarEstadoAutenticacion($documento, $nuevoEstado) {
        $doc = mysqli_real_escape_string($this->db, $documento);
        $estado = mysqli_real_escape_string($this->db, $nuevoEstado);
        
        // Verificar si existe registro
        $check = mysqli_query($this->db, "SELECT * FROM registro_autenticacion WHERE documento='$doc'");
        
        if (mysqli_num_rows($check) > 0) {
            $query = "UPDATE registro_autenticacion SET estado='$estado' WHERE documento='$doc'";
        } else {
            $query = "INSERT INTO registro_autenticacion (documento, estado) VALUES ('$doc','$estado')";
        }
        
        return mysqli_query($this->db, $query);
    }
}
?>
