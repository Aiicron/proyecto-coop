<?php
class Comprobante {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // Obtener todos los comprobantes con datos del usuario
    public function obtenerTodosConUsuario() {
        $query = "SELECT c.id_comprobante, c.documento, u.nombre, c.archivo_pdf, c.estado, c.tipo
                  FROM comprobante_pago c
                  INNER JOIN usuarios u ON c.documento = u.documento
                  ORDER BY c.id_comprobante DESC";
        $resultado = mysqli_query($this->db, $query);
        
        $comprobantes = [];
        while ($row = mysqli_fetch_assoc($resultado)) {
            $comprobantes[] = $row;
        }
        return $comprobantes;
    }

    // Aprobar comprobante
    public function aprobar($id_comprobante) {
        $id = mysqli_real_escape_string($this->db, $id_comprobante);
        $query = "UPDATE comprobante_pago SET estado='aprobado' WHERE id_comprobante='$id'";
        return mysqli_query($this->db, $query);
    }

    // Rechazar comprobante
    public function rechazar($id_comprobante) {
        $id = mysqli_real_escape_string($this->db, $id_comprobante);
        $query = "UPDATE comprobante_pago SET estado='rechazado' WHERE id_comprobante='$id'";
        return mysqli_query($this->db, $query);
    }

    // Obtener documento del dueño del comprobante
    public function obtenerDocumentoPorId($id_comprobante) {
        $id = mysqli_real_escape_string($this->db, $id_comprobante);
        $query = "SELECT documento FROM comprobante_pago WHERE id_comprobante='$id' LIMIT 1";
        $resultado = mysqli_query($this->db, $query);
        
        if ($resultado && mysqli_num_rows($resultado) > 0) {
            return mysqli_fetch_assoc($resultado)['documento'];
        }
        return null;
    }

    // Subir comprobante inicial
    public function subirInicial($documento, $archivo) {
        // Verificar si el archivo es un PDF
        $extension = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));
        
        if ($extension !== "pdf") {
            return " Solo se permiten archivos PDF.";
        }

        // Verificar tamaño (máximo 5MB)
        if ($archivo["size"] > 5000000) {
            return " El archivo es demasiado grande. Máximo 5MB.";
        }

        // Crear carpeta si no existe
        $carpetaDestino = __DIR__ . "/../../uploads/";
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }

        // Generar nombre único para el archivo
        $nombreArchivo = "comprobante_inicial_" . $documento . "_" . time() . ".pdf";
        $rutaCompleta = $carpetaDestino . $nombreArchivo;

        // Mover archivo
        if (move_uploaded_file($archivo["tmp_name"], $rutaCompleta)) {
            // Guardar en la base de datos
            $doc = mysqli_real_escape_string($this->db, $documento);
            $rutaBD = "uploads/" . $nombreArchivo;
            
            $query = "INSERT INTO comprobante_pago (documento, tipo, archivo_pdf, estado) 
                      VALUES ('$doc', 'inicial', '$rutaBD', 'pendiente')";
            
            if (mysqli_query($this->db, $query)) {
                return " Comprobante subido exitosamente. En espera de aprobación.";
            } else {
                return " Error al guardar en la base de datos.";
            }
        } else {
            return " Error al subir el archivo.";
        }
    }

    // Obtener estado del comprobante inicial de un usuario
    public function obtenerEstado($documento) {
        $doc = mysqli_real_escape_string($this->db, $documento);
        $query = "SELECT estado FROM comprobante_pago 
                  WHERE documento='$doc' AND tipo='inicial' 
                  ORDER BY id_comprobante DESC LIMIT 1";
        
        $resultado = mysqli_query($this->db, $query);
        
        if ($resultado && mysqli_num_rows($resultado) > 0) {
            return mysqli_fetch_assoc($resultado)['estado'];
        }
        
        return null; // No tiene comprobante subido
    }

    // Verificar si el usuario tiene comprobante inicial aprobado
    public function tieneComprobanteInicialAprobado($documento) {
        $doc = mysqli_real_escape_string($this->db, $documento);
        $query = "SELECT * FROM comprobante_pago 
                  WHERE documento='$doc' AND tipo='inicial' AND estado='aprobado' 
                  LIMIT 1";
        
        $resultado = mysqli_query($this->db, $query);
        return ($resultado && mysqli_num_rows($resultado) > 0);
    }


    // Subir comprobante mensual
    public function subirMensual($documento, $archivo) {
        // Verificar si el archivo es un PDF
        $extension = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));
        
        if ($extension !== "pdf") {
            return " Solo se permiten archivos PDF.";
        }

        // Verificar tamaño (máximo 5MB)
        if ($archivo["size"] > 5000000) {
            return " El archivo es demasiado grande. Máximo 5MB.";
        }

        // Crear carpeta si no existe
        $carpetaDestino = __DIR__ . "/../../uploads/";
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }

        // Generar nombre único para el archivo
        $nombreArchivo = "comprobante_mensual_" . $documento . "_" . time() . ".pdf";
        $rutaCompleta = $carpetaDestino . $nombreArchivo;

        // Mover archivo
        if (move_uploaded_file($archivo["tmp_name"], $rutaCompleta)) {
            // Guardar en la base de datos
            $doc = mysqli_real_escape_string($this->db, $documento);
            $rutaBD = "uploads/" . $nombreArchivo;
            
            $query = "INSERT INTO comprobante_pago (documento, tipo, archivo_pdf, estado, fecha_subida) 
                      VALUES ('$doc', 'mensual', '$rutaBD', 'pendiente', NOW())";
            
            if (mysqli_query($this->db, $query)) {
                return " Comprobante mensual subido exitosamente. En espera de aprobación.";
            } else {
                return " Error al guardar en la base de datos.";
            }
        } else {
            return " Error al subir el archivo.";
        }
    }

    // Obtener historial de comprobantes mensuales de un usuario
    public function obtenerHistorialMensuales($documento) {
        $doc = mysqli_real_escape_string($this->db, $documento);
        $query = "SELECT archivo_pdf, estado, fecha_subida 
                  FROM comprobante_pago 
                  WHERE documento='$doc' AND tipo='mensual' 
                  ORDER BY fecha_subida DESC";
        
        $resultado = mysqli_query($this->db, $query);
        
        $historial = [];
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $historial[] = $row;
            }
        }
        
        return $historial;
    }

    // Obtener los últimos 3 comprobantes de un usuario
    public function obtenerUltimosComprobantes($documento, $limite = 3) {
        $doc = mysqli_real_escape_string($this->db, $documento);
        $limite = (int)$limite;
        
        $query = "SELECT tipo, estado, fecha_subida, archivo_pdf 
                  FROM comprobante_pago 
                  WHERE documento='$doc' 
                  ORDER BY fecha_subida DESC 
                  LIMIT $limite";
        
        $resultado = mysqli_query($this->db, $query);
        
        $comprobantes = [];
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $comprobantes[] = $row;
            }
        }
        
        return $comprobantes;
    }
}
?>

