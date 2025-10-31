<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Modelos/Usuario.php';

class LoginController {
    private $db;
    private $usuarioModel;

    public function __construct($enlace) {
        $this->db = $enlace;
        $this->usuarioModel = new Usuario($enlace);
    }

    public function mostrarLogin() {
        $mensaje = "";
        $mensajeClase = "";
        require __DIR__ . '/../Vistas/login.php';
    }

    public function login() {
        $mensaje = "";
        $mensajeClase = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $correo = $_POST['correo'] ?? '';
            $contrasena = $_POST['contrasena'] ?? '';

            // Obtener usuario por correo usando el modelo
            $usuario = $this->usuarioModel->obtenerPorCorreo($correo);

            if ($usuario) {
                // Verificar contraseña
                if ($usuario['contrasena'] === $contrasena) {
                    // Obtener estado de autenticación
                    $estado = $this->usuarioModel->obtenerEstadoAutenticacion($usuario['documento']);

                    if ($estado === "aceptado") {
                        // Login exitoso
                        $_SESSION['documento'] = $usuario['documento'];
                        $_SESSION['nombre'] = $usuario['nombre'];

                        // Redirigir a inicial
                        header("Location: index.php?page=inicial");
                        exit();

                    } elseif ($estado === "pendiente") {
                        $mensaje = "Tu solicitud aún está pendiente de aprobación.";
                        $mensajeClase = "error";
                    } else {
                        $mensaje = "Tu solicitud fue rechazada. Contacta a la administración.";
                        $mensajeClase = "error";
                    }
                } else {
                    $mensaje = "Contraseña incorrecta.";
                    $mensajeClase = "error";
                }
            } else {
                $mensaje = "Usuario no encontrado.";
                $mensajeClase = "error";
            }
        }

        // Cargar la vista con los mensajes
        require __DIR__ . '/../Vistas/login.php';
    }
}
?>