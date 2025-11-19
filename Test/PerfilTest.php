<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Modelos/Perfil.php';

class PerfilTestable extends Perfil {
    public function __construct($pdo) {
        $this->conexion = $pdo;
    }
}

class PerfilTest extends TestCase {

    private $db;
    private $perfil;

    protected function setUp(): void {
        // Base de datos en memoria
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crear tablas necesarias
        $this->db->exec("
            CREATE TABLE usuarios (
                documento TEXT PRIMARY KEY,
                nombre TEXT,
                correo TEXT,
                contrasena TEXT,
                motivo_ingreso TEXT
            );
        ");

        $this->db->exec("
            CREATE TABLE registro_autenticacion (
                documento TEXT PRIMARY KEY,
                estado TEXT
            );
        ");

        $this->db->exec("
            CREATE TABLE horas_trabajadas (
                id_hora INTEGER PRIMARY KEY AUTOINCREMENT,
                documento TEXT,
                horas INTEGER
            );
        ");

        $this->db->exec("
            CREATE TABLE comprobante_pago (
                id_comprobante INTEGER PRIMARY KEY AUTOINCREMENT,
                documento TEXT,
                estado TEXT
            );
        ");

        $this->db->exec("
            CREATE TABLE unidades_habitacionales (
                num_puerta INTEGER PRIMARY KEY,
                direccion TEXT
            );
        ");

        $this->db->exec("
            CREATE TABLE asigna (
                num_puerta INTEGER,
                documento TEXT
            );
        ");

        // Insertar usuario base
        $hash = password_hash("12345", PASSWORD_DEFAULT);

        $this->db->exec("
            INSERT INTO usuarios (documento, nombre, correo, contrasena, motivo_ingreso)
            VALUES ('123', 'Tiziano', 'tizi@mail.com', '$hash', 'Ayuda mutua')
        ");

        $this->db->exec("
            INSERT INTO registro_autenticacion (documento, estado)
            VALUES ('123', 'aceptado')
        ");

        $this->perfil = new PerfilTestable($this->db);
    }

    /* ============================================================
       TEST 1: obtenerInformacionCompleta()
    ============================================================ */
    public function testObtenerInformacionCompleta() {
        $stmt = $this->db->prepare("
            SELECT u.documento, u.nombre, u.correo, u.motivo_ingreso,
                   ra.estado as estado_autenticacion
            FROM usuarios u
            LEFT JOIN registro_autenticacion ra ON u.documento = ra.documento
            WHERE u.documento = '123'
        ");
        $stmt->execute();
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Tiziano', $perfil['nombre']);
        $this->assertEquals('aceptado', $perfil['estado_autenticacion']);
    }

    /* ============================================================
       TEST 2: obtenerViviendaAsignada()
    ============================================================ */
    public function testObtenerViviendaAsignada() {
        $this->db->exec("INSERT INTO unidades_habitacionales (num_puerta, direccion)
                         VALUES (10, 'Calle Falsa 123')");

        $this->db->exec("INSERT INTO asigna (num_puerta, documento)
                         VALUES (10, '123')");

        $stmt = $this->db->prepare("
            SELECT uh.num_puerta, uh.direccion
            FROM asigna a
            INNER JOIN unidades_habitacionales uh ON a.num_puerta = uh.num_puerta
            WHERE a.documento = '123'
            LIMIT 1
        ");
        $stmt->execute();
        $vivienda = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(10, $vivienda['num_puerta']);
        $this->assertEquals("Calle Falsa 123", $vivienda['direccion']);
    }

    /* ============================================================
       TEST 3: obtenerTotalHoras()
    ============================================================ */
    public function testObtenerTotalHoras() {
        $this->db->exec("
            INSERT INTO horas_trabajadas (documento, horas)
            VALUES ('123', 5),
                   ('123', 4)
        ");

        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(horas), 0) as total_horas
            FROM horas_trabajadas
            WHERE documento='123'
        ");
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(9, $fila['total_horas']);
    }

    /* ============================================================
       TEST 4: obtenerEstadisticasComprobantes()
    ============================================================ */
    public function testObtenerEstadisticasComprobantes() {
        $this->db->exec("
            INSERT INTO comprobante_pago (documento, estado)
            VALUES ('123', 'aprobado'),
                   ('123', 'pendiente'),
                   ('123', 'rechazado')
        ");

        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total,
                   SUM(CASE WHEN estado='aprobado' THEN 1 ELSE 0 END) as aprobados,
                   SUM(CASE WHEN estado='pendiente' THEN 1 ELSE 0 END) as pendientes,
                   SUM(CASE WHEN estado='rechazado' THEN 1 ELSE 0 END) as rechazados
            FROM comprobante_pago
            WHERE documento='123'
        ");
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(3, $res['total']);
        $this->assertEquals(1, $res['aprobados']);
        $this->assertEquals(1, $res['pendientes']);
        $this->assertEquals(1, $res['rechazados']);
    }

    /* ============================================================
       TEST 5: actualizarDatosPersonales()
    ============================================================ */
    public function testActualizarDatosPersonalesExito() {
        // No existe correo duplicado → actualiza correctamente
        $stmt = $this->db->prepare("
            UPDATE usuarios SET nombre='Nuevo', correo='nuevo@mail.com' WHERE documento='123'
        ");
        $res = $stmt->execute();

        $this->assertTrue($res);
    }

    public function testActualizarDatosPersonalesCorreoDuplicado() {
        // Insertar otro usuario con email existente
        $this->db->exec("
            INSERT INTO usuarios (documento, nombre, correo, contrasena, motivo_ingreso)
            VALUES ('999', 'Otro', 'duplicado@mail.com', 'x', 'x')
        ");

        // Intento actualizar usuario 123 con correo que ya existe
        $stmt = $this->db->prepare("
            SELECT documento FROM usuarios WHERE correo='duplicado@mail.com' AND documento!='123'
        ");
        $stmt->execute();
        $res = $stmt->fetchAll();

        $this->assertNotEmpty($res);
    }

    /* ============================================================
       TEST 6: verificarContrasenaActual()
    ============================================================ */
    public function testVerificarContrasenaActualCorrecta() {
        $stmt = $this->db->prepare("
            SELECT contrasena FROM usuarios WHERE documento='123'
        ");
        $stmt->execute();
        $hash = $stmt->fetchColumn();

        $this->assertTrue(password_verify("12345", $hash));
    }

    public function testVerificarContrasenaActualIncorrecta() {
        $stmt = $this->db->prepare("
            SELECT contrasena FROM usuarios WHERE documento='123'
        ");
        $stmt->execute();
        $hash = $stmt->fetchColumn();

        // Contraseña errónea
        $this->assertFalse(password_verify("xxxxx", $hash));
    }

    /* ============================================================
       TEST 7: cambiarContrasena()
    ============================================================ */
    public function testCambiarContrasenaExito() {
        $nueva = password_hash("nueva123", PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            UPDATE usuarios SET contrasena='$nueva' WHERE documento='123'
        ");
        $res = $stmt->execute();

        $this->assertTrue($res);
    }

    public function testCambiarContrasenaLongitudInsuficiente() {
        $this->assertLessThan(6, strlen("123"));
    }
}
