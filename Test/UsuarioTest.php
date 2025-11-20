<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Modelos/Usuario.php';

class UsuarioTest extends TestCase
{
    /** @var mysqli */
    private $db;

    /** @var Usuario */
    private $usuario;

    protected function setUp(): void
    {
        // ⚠️ Ajustá estos datos a tu entorno de XAMPP/MAMP
        $host = 'localhost';
        $user = 'root';
        $pass = '';          // o 'root' según tu caso
        $dbname = 'tat_test'; // BD SOLO PARA TESTING

        $this->db = new mysqli($host, $user, $pass);

        if ($this->db->connect_errno) {
            $this->fail("Error de conexión MySQL: " . $this->db->connect_error);
        }

        // Crear BD de test (si no existe) y usarla
        $this->db->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
        $this->db->select_db($dbname);
        $this->db->set_charset("utf8mb4");

        // Limpiar y crear tablas necesarias
        $this->db->query("DROP TABLE IF EXISTS administrativo");
        $this->db->query("DROP TABLE IF EXISTS comprobante_pago");
        $this->db->query("DROP TABLE IF EXISTS registro_autenticacion");
        $this->db->query("DROP TABLE IF EXISTS usuarios");

        $this->db->query("
            CREATE TABLE usuarios (
                documento VARCHAR(20) PRIMARY KEY,
                nombre VARCHAR(100),
                correo VARCHAR(100) UNIQUE,
                contrasena VARCHAR(255),
                motivo_ingreso TEXT
            )
        ");

        $this->db->query("
            CREATE TABLE registro_autenticacion (
                documento VARCHAR(20) PRIMARY KEY,
                estado VARCHAR(20)
            )
        ");

        $this->db->query("
            CREATE TABLE comprobante_pago (
                id_comprobante INT AUTO_INCREMENT PRIMARY KEY,
                documento VARCHAR(20),
                tipo VARCHAR(20),
                archivo_pdf VARCHAR(255),
                estado VARCHAR(20)
            )
        ");

        $this->db->query("
            CREATE TABLE administrativo (
                documento VARCHAR(20) PRIMARY KEY
            )
        ");

        // Datos base
        $passHash = password_hash('secreta123', PASSWORD_DEFAULT);

        // Usuario normal
        $this->db->query("
            INSERT INTO usuarios (documento, nombre, correo, contrasena, motivo_ingreso)
            VALUES ('123', 'Tiziano', 'tizi@example.com', '$passHash', 'Prueba de ingreso')
        ");

        // Usuario admin
        $this->db->query("
            INSERT INTO usuarios (documento, nombre, correo, contrasena, motivo_ingreso)
            VALUES ('999', 'Admin', 'admin@example.com', '$passHash', 'Admin del sistema')
        ");
        $this->db->query("
            INSERT INTO administrativo (documento) VALUES ('999')
        ");

        // Registro de autenticación
        $this->db->query("
            INSERT INTO registro_autenticacion (documento, estado)
            VALUES ('123', 'pendiente')
        ");

        // Comprobante inicial aprobado
        $this->db->query("
            INSERT INTO comprobante_pago (documento, tipo, archivo_pdf, estado)
            VALUES ('123', 'inicial', 'uploads/comp.pdf', 'aprobado')
        ");

        $this->usuario = new Usuario($this->db);
    }

    protected function tearDown(): void
    {
        if ($this->db instanceof mysqli) {
            $this->db->close();
        }
    }

    /* ============================================================
       existeUsuario()
    ============================================================ */

    public function testExisteUsuarioDevuelveTrueSiDocumentoYaExiste()
    {
        $existe = $this->usuario->existeUsuario('123', 'otrocorreo@example.com');
        $this->assertTrue($existe);
    }

    public function testExisteUsuarioDevuelveTrueSiCorreoYaExiste()
    {
        $existe = $this->usuario->existeUsuario('000', 'tizi@example.com');
        $this->assertTrue($existe);
    }

    public function testExisteUsuarioDevuelveFalseSiNoExiste()
    {
        $existe = $this->usuario->existeUsuario('111', 'nuevo@example.com');
        $this->assertFalse($existe);
    }

    /* ============================================================
       registrar()
    ============================================================ */

    public function testRegistrarInsertaNuevoUsuario()
    {
        $resultado = $this->usuario->registrar(
            'Nuevo User',
            'nuevo@example.com',
            'hashX',
            '555',
            'Motivo test'
        );

        $this->assertTrue($resultado);

        $res = $this->db->query("SELECT * FROM usuarios WHERE documento='555'");
        $this->assertEquals(1, $res->num_rows);
    }

    /* ============================================================
       registrarAutenticacion()
    ============================================================ */

    public function testRegistrarAutenticacionCreaRegistroPendiente()
    {
        $resultado = $this->usuario->registrarAutenticacion('555');
        $this->assertTrue($resultado);

        $res = $this->db->query("SELECT estado FROM registro_autenticacion WHERE documento='555'");
        $this->assertEquals(1, $res->num_rows);

        $fila = $res->fetch_assoc();
        $this->assertEquals('pendiente', $fila['estado']);
    }

    /* ============================================================
       obtenerPorCorreo()
    ============================================================ */

    public function testObtenerPorCorreoDevuelveUsuarioCuandoExiste()
    {
        $user = $this->usuario->obtenerPorCorreo('tizi@example.com');

        $this->assertNotNull($user);
        $this->assertEquals('123', $user['documento']);
        $this->assertEquals('Tiziano', $user['nombre']);
    }

    public function testObtenerPorCorreoDevuelveNullCuandoNoExiste()
    {
        $user = $this->usuario->obtenerPorCorreo('noexiste@example.com');
        $this->assertNull($user);
    }

    /* ============================================================
       obtenerPorDocumento()
    ============================================================ */

    public function testObtenerPorDocumentoDevuelveUsuarioCuandoExiste()
    {
        $user = $this->usuario->obtenerPorDocumento('123');

        $this->assertNotNull($user);
        $this->assertEquals('tizi@example.com', $user['correo']);
    }

    public function testObtenerPorDocumentoDevuelveNullCuandoNoExiste()
    {
        $user = $this->usuario->obtenerPorDocumento('000');
        $this->assertNull($user);
    }

    /* ============================================================
       obtenerEstadoAutenticacion()
    ============================================================ */

    public function testObtenerEstadoAutenticacionDevuelveEstadoCuandoExiste()
    {
        $estado = $this->usuario->obtenerEstadoAutenticacion('123');
        $this->assertEquals('pendiente', $estado);
    }

    public function testObtenerEstadoAutenticacionDevuelveNullCuandoNoExiste()
    {
        $estado = $this->usuario->obtenerEstadoAutenticacion('555');
        $this->assertNull($estado);
    }

    /* ============================================================
       tieneComprobanteInicialAprobado()
    ============================================================ */

    public function testTieneComprobanteInicialAprobadoTrueCuandoHayRegistro()
    {
        $tiene = $this->usuario->tieneComprobanteInicialAprobado('123');
        $this->assertTrue($tiene);
    }

    public function testTieneComprobanteInicialAprobadoFalseCuandoNoHayRegistro()
    {
        $tiene = $this->usuario->tieneComprobanteInicialAprobado('555');
        $this->assertFalse($tiene);
    }

    /* ============================================================
       esAdministrador()
    ============================================================ */

    public function testEsAdministradorDevuelveTrueParaDocumentoAdmin()
    {
        $esAdmin = $this->usuario->esAdministrador('999');
        $this->assertTrue($esAdmin);
    }

    public function testEsAdministradorDevuelveFalseParaDocumentoNoAdmin()
    {
        $esAdmin = $this->usuario->esAdministrador('123');
        $this->assertFalse($esAdmin);
    }

    /* ============================================================
       obtenerTodosConEstado()
    ============================================================ */

    public function testObtenerTodosConEstadoDevuelveListadoDeUsuariosNoAdministrativos()
    {
        $usuarios = $this->usuario->obtenerTodosConEstado();

        $this->assertNotEmpty($usuarios);

        // En la tabla administrativa solo está 999, así que no debería venir
        $documentos = array_column($usuarios, 'documento');
        $this->assertContains('123', $documentos);
        $this->assertNotContains('999', $documentos);
    }

    /* ============================================================
       cambiarEstadoAutenticacion()
    ============================================================ */

    public function testCambiarEstadoAutenticacionActualizaRegistroExistente()
    {
        $resultado = $this->usuario->cambiarEstadoAutenticacion('123', 'aceptado');
        $this->assertTrue($resultado);

        $res = $this->db->query("SELECT estado FROM registro_autenticacion WHERE documento='123'");
        $fila = $res->fetch_assoc();

        $this->assertEquals('aceptado', $fila['estado']);
    }

    public function testCambiarEstadoAutenticacionInsertaSiNoExiste()
    {
        $resultado = $this->usuario->cambiarEstadoAutenticacion('555', 'rechazado');
        $this->assertTrue($resultado);

        $res = $this->db->query("SELECT estado FROM registro_autenticacion WHERE documento='555'");
        $this->assertEquals(1, $res->num_rows);

        $fila = $res->fetch_assoc();
        $this->assertEquals('rechazado', $fila['estado']);
    }
}
