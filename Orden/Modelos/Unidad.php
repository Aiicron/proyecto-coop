<?php
class Unidad {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Obtener una unidad disponible (sin asignar)
    public function obtenerDisponible() {
        $query = "SELECT num_puerta FROM unidades_habitacionales 
                  WHERE num_puerta NOT IN (SELECT num_puerta FROM asigna) 
                  LIMIT 1";
        $resultado = mysqli_query($this->db, $query);
        
        if ($resultado && mysqli_num_rows($resultado) > 0) {
            return mysqli_fetch_assoc($resultado)['num_puerta'];
        }
        return null;
    }

    // Asignar unidad a un usuario
    public function asignar($num_puerta, $documento) {
        $puerta = mysqli_real_escape_string($this->db, $num_puerta);
        $doc = mysqli_real_escape_string($this->db, $documento);
        $query = "INSERT INTO asigna (num_puerta, documento) VALUES ('$puerta', '$doc')";
        return mysqli_query($this->db, $query);
    }

    // Obtener todas las unidades con su estado de asignación
    public function obtenerTodasConEstado() {
        $query = "SELECT u.num_puerta, a.documento, us.nombre
                  FROM unidades_habitacionales u
                  LEFT JOIN asigna a ON u.num_puerta = a.num_puerta
                  LEFT JOIN usuarios us ON a.documento = us.documento
                  ORDER BY u.num_puerta ASC";
        
        $resultado = mysqli_query($this->db, $query);
        
        $unidades = [];
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $unidades[] = $row;
            }
        }
        return $unidades;
    }

    // Obtener usuarios sin unidad asignada
    public function obtenerUsuariosSinUnidad() {
        $query = "SELECT u.documento, u.nombre
                  FROM usuarios u
                  INNER JOIN registro_autenticacion r ON u.documento = r.documento
                  WHERE r.estado = 'aceptado'
                  AND u.documento NOT IN (SELECT documento FROM asigna)
                  ORDER BY u.nombre ASC";
        
        $resultado = mysqli_query($this->db, $query);
        
        $usuarios = [];
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $usuarios[] = $row;
            }
        }
        return $usuarios;
    }

    // Desasignar una unidad
    public function desasignar($num_puerta) {
        $puerta = mysqli_real_escape_string($this->db, $num_puerta);
        $query = "DELETE FROM asigna WHERE num_puerta='$puerta'";
        return mysqli_query($this->db, $query);
    }

    // Verificar si una unidad está asignada
    public function estaAsignada($num_puerta) {
        $puerta = mysqli_real_escape_string($this->db, $num_puerta);
        $query = "SELECT * FROM asigna WHERE num_puerta='$puerta' LIMIT 1";
        $resultado = mysqli_query($this->db, $query);
        return ($resultado && mysqli_num_rows($resultado) > 0);
    }
}
?>