<?php
require_once __DIR__ . '/../Modelos/Perfil.php';

class PerfilController {
    private $modeloPerfil;

    public function __construct($conexion) {
        $this->modeloPerfil = new Perfil($conexion);
    }

    /**
     * Muestra la página de perfil del usuario
     */
    public function mostrarPerfil() {
        // Verificar sesión
        if (!isset($_SESSION['documento'])) {
            header("Location: index.php?page=login");
            exit();
        }

        $documento = $_SESSION['documento'];

        // Obtener toda la información del usuario
        $informacion = $this->modeloPerfil->obtenerInformacionCompleta($documento);
        $vivienda = $this->modeloPerfil->obtenerViviendaAsignada($documento);
        $totalHoras = $this->modeloPerfil->obtenerTotalHoras($documento);
        $estadisticasComprobantes = $this->modeloPerfil->obtenerEstadisticasComprobantes($documento);

        // Cargar vista
        require_once __DIR__ . '/../Vistas/perfil.php';
    }

    /**
     * Actualiza los datos personales del usuario
     */
    public function actualizarDatos() {
        // Verificar sesión
        if (!isset($_SESSION['documento'])) {
            header("Location: index.php?page=login");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: index.php?page=perfil");
            exit();
        }

        $documento = $_SESSION['documento'];
        $nombre = trim($_POST['nombre']);
        $apellido1 = trim($_POST['apellido1']);
        $apellido2 = trim($_POST['apellido2']);
        $email = trim($_POST['email']);

        // Validar campos requeridos
        if (empty($nombre) || empty($email)) {
            $_SESSION['mensaje_perfil'] = 'error|⚠️ El nombre y el email son obligatorios.';
            header("Location: index.php?page=perfil");
            exit();
        }

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['mensaje_perfil'] = 'error|⚠️ El formato del email es inválido.';
            header("Location: index.php?page=perfil");
            exit();
        }

        // Actualizar datos
        $resultado = $this->modeloPerfil->actualizarDatosPersonales($documento, $nombre, $apellido1, $apellido2, $email);

        if ($resultado['success']) {
            // Actualizar nombre en sesión
            $_SESSION['nombre'] = $nombre;
            $_SESSION['mensaje_perfil'] = 'success|' . $resultado['mensaje'];
        } else {
            $_SESSION['mensaje_perfil'] = 'error|' . $resultado['mensaje'];
        }

        header("Location: index.php?page=perfil");
        exit();
    }

    /**
     * Cambia la contraseña del usuario
     */
    public function cambiarContrasena() {
        // Verificar sesión
        if (!isset($_SESSION['documento'])) {
            header("Location: index.php?page=login");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: index.php?page=perfil");
            exit();
        }

        $documento = $_SESSION['documento'];
        $contrasenaActual = $_POST['contrasena_actual'];
        $contrasenaNueva = $_POST['contrasena_nueva'];
        $contrasenaConfirmar = $_POST['contrasena_confirmar'];

        // Validar que las contraseñas coincidan
        if ($contrasenaNueva !== $contrasenaConfirmar) {
            $_SESSION['mensaje_perfil'] = 'error|⚠️ Las contraseñas nuevas no coinciden.';
            header("Location: index.php?page=perfil");
            exit();
        }

        // Cambiar contraseña
        $resultado = $this->modeloPerfil->cambiarContrasena($documento, $contrasenaActual, $contrasenaNueva);

        if ($resultado['success']) {
            $_SESSION['mensaje_perfil'] = 'success|' . $resultado['mensaje'];
        } else {
            $_SESSION['mensaje_perfil'] = 'error|' . $resultado['mensaje'];
        }

        header("Location: index.php?page=perfil");
        exit();
    }
}