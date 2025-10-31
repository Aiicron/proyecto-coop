<?php
// Mostrar errores
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
$enlace = require_once __DIR__ . "/Config/database.php";

// Cargar controladores
require_once __DIR__ . "/Controladores/RegistroController.php";
require_once __DIR__ . "/Controladores/UsuarioControlador.php";
require_once __DIR__ . "/Controladores/LoginController.php";
require_once __DIR__ . "/Controladores/InicialController.php";
require_once __DIR__ . "/Controladores/BackofficeController.php";
require_once __DIR__ . "/Controladores/BienvenidaController.php"; 
require_once __DIR__ . "/Controladores/PagosController.php";
require_once __DIR__ . "/Controladores/HorasController.php";

// Instanciar controladores
$registroController = new RegistroController($enlace);
$usuarioController = new UsuarioControlador();
$loginController = new LoginController($enlace);
$inicialController = new InicialController($enlace);
$backofficeController = new BackofficeController($enlace);
$bienvenidaController = new BienvenidaController($enlace); 
$pagosController = new PagosController($enlace);
$horasController = new HorasController($enlace);

// Obtener parámetros
$page = $_GET['page'] ?? 'form';
$accion = $_GET['accion'] ?? null;

// --- Acciones POST ---
if ($accion === 'registrar') {
    $registroController->registrar();
    exit;
}

if ($accion === 'login') {
    $loginController->login();
    exit;
}

if ($accion === 'logout') {
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}

if ($accion === 'cambiar_estado_usuario') {
    $backofficeController->cambiarEstadoUsuario();
    exit;
}

if ($accion === 'gestionar_comprobante') {
    $backofficeController->gestionarComprobante();
    exit;
}

if ($accion === 'registrar_horas') {
    $horasController->registrarHoras();
    exit;
}

if ($accion === 'asignar_unidad') {
    $backofficeController->asignarUnidad();
    exit;
}

if ($accion === 'desasignar_unidad') {
    $backofficeController->desasignarUnidad();
    exit;
}

// --- Páginas ---
switch ($page) {
    case 'nosotros':
        $usuarioController->mostrarNosotros();
        break;
    
    case 'login':
        $loginController->mostrarLogin();
        break;
    
    case 'inicial':
        $inicialController->mostrarInicial();
        break;
    
    case 'backoffice':
        $backofficeController->mostrarBackoffice();
        break;
    
    case 'bienvenida': 
        $bienvenidaController->mostrarBienvenida();
        break;

    case 'pagos': 
        $pagosController->mostrarPagos();
        break;
    
    case 'horas':
        $horasController->mostrarPagina();
        break;
    
    case 'form':
    default:
        $registroController->mostrarFormulario();
        break;
}