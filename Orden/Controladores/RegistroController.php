<?php
require_once 'Modelos/Usuario.php';

class RegistroController {
    private $modelo;

    public function __construct($db) {
        $this->modelo = new Usuario($db);
    }

    public function mostrarFormulario($mensaje = "") {
        require 'Vistas/form.php';
    }

    public function registrar() {
        $mensaje = "";
        $nombre = mysqli_real_escape_string($this->modelo->db, $_POST['nombre']);
        $correo = mysqli_real_escape_string($this->modelo->db, $_POST['correo']);
        $contrasena = mysqli_real_escape_string($this->modelo->db, $_POST['contrasena']);
        $documento = mysqli_real_escape_string($this->modelo->db, $_POST['cedula']);
        $motivo = mysqli_real_escape_string($this->modelo->db, $_POST['comentario']);

        if ($this->modelo->existeUsuario($documento, $correo)) {
            $mensaje = "<p class='error'>El documento o correo ya está en uso. Intente con otro.</p>";
        } else {
            if ($this->modelo->registrar($nombre, $correo, $contrasena, $documento, $motivo)) {
                if ($this->modelo->registrarAutenticacion($documento)) {
                    $mensaje = "<p class='success'> Solicitud enviada con éxito. En 48h recibirá un correo si es aprobada.</p>";
                } else {
                    $mensaje = "<p class='error'>Error al registrar autenticación.</p>";
                }
            } else {
                $mensaje = "<p class='error'>Error al registrar usuario.</p>";
            }
        }

        $this->mostrarFormulario($mensaje);
    }
}
?>
