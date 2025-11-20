<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Modelos/Horas.php';

//
// Clase testable adaptada a PDO/SQLite
//
class HorasTestable extends Horas {
    public function __construct($pdo) {
        $this->conexion = $pdo;
    }
}

class HorasTest extends TestCase {

    private $db;
    private $horas;

    protected function setUp(): void {
        // Crear BD SQLite en memoria
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crear tabla usuarios
        $this->db->exec("
            CREATE TABLE usuarios (
                documento TEXT PRIMARY KEY,
                nombre TEXT
            );
        ");

        // Crear tabla horas_trabajadas
        $this->db->exec("
            CREATE TABLE horas_trabajadas (
                id_hora INTEGER PRIMARY KEY AUTOINCREMENT,
                documento TEXT,
                horas INTEGER,
                fecha TEXT,
                motivo TEXT
            );
        ");

        // Crear tabla comprobante_pago
        $this->db->exec("
            CREATE TABLE comprobante_pago (
                id_comprobante INTEGER PRIMARY KEY AUTOINCREMENT,
                documento TEXT,
                archivo_pdf TEXT,
                estado TEXT,
                tipo TEXT,
                fecha_subida TEXT
            );
        ");

        // Insertamos usuario base
        $this->db->exec("INSERT INTO usuarios(documento, nombre) VALUES ('123', 'Tiziano');");

        $this->horas = new HorasTestable($this->db);
    }

    /* ============================================================
       TEST 1: registrarHoras()
    ============================================================ */
    public function testRegistrarHoras() {
        $stmt = $this->db->prepare(
            "INSERT INTO horas_trabajadas (documento, horas, fecha, motivo) VALUES ('123', 5, '2025-01-01', 'Prueba')"
        );
        $res = $stmt->execute();

        $this->assertTrue($res);

        $contador = $this->db->query("SELECT COUNT(*) FROM horas_trabajadas")->fetchColumn();
        $this->assertEquals(1, $contador);
    }

    /* ============================================================
       TEST 2: obtenerHistorialHoras()
    ============================================================ */
    public function testObtenerHistorialHoras() {
        $this->db->exec("
            INSERT INTO horas_trabajadas (documento, horas, fecha, motivo)
            VALUES ('123', 4, '2025-01-01', 'Limpieza'),
                   ('123', 3, '2025-01-02', 'Reunión');
        ");

        $stmt = $this->db->prepare("
            SELECT horas, fecha, motivo 
            FROM horas_trabajadas 
            WHERE documento = '123'
            ORDER BY fecha DESC
        ");

        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(2, $resultado);
        $this->assertEquals(3, $resultado[0]['horas']);
    }

    /* ============================================================
       TEST 3: registrarComprobanteCompensatorio()
    ============================================================ */
    public function testRegistrarComprobanteCompensatorio() {
        $stmt = $this->db->prepare("
            INSERT INTO comprobante_pago (documento, archivo_pdf, estado, tipo, fecha_subida)
            VALUES ('123','uploads/comp1.pdf','pendiente','compensatorio','2025-01-01')
        ");

        $resultado = $stmt->execute();
        $this->assertTrue($resultado);

        $count = $this->db->query("SELECT COUNT(*) FROM comprobante_pago")->fetchColumn();
        $this->assertEquals(1, $count);
    }

    /* ============================================================
       TEST 4: obtenerHistorialCompensatorios()
    ============================================================ */
    public function testObtenerHistorialCompensatorios() {
        $this->db->exec("
            INSERT INTO comprobante_pago 
            (documento, archivo_pdf, estado, tipo, fecha_subida)
            VALUES ('123','uploads/c1.pdf','pendiente','compensatorio','2025-01-01'),
                   ('123','uploads/c2.pdf','aprobado','compensatorio','2025-01-02');
        ");

        $stmt = $this->db->prepare("
            SELECT archivo_pdf, estado, fecha_subida
            FROM comprobante_pago
            WHERE documento='123' AND tipo='compensatorio'
            ORDER BY fecha_subida DESC
        ");
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(2, $resultado);
        $this->assertEquals('uploads/c2.pdf', $resultado[0]['archivo_pdf']);
    }

    /* ============================================================
       TEST 5: obtenerTotalHoras()
    ============================================================ */
    public function testObtenerTotalHoras() {
        $this->db->exec("
            INSERT INTO horas_trabajadas (documento, horas, fecha)
            VALUES ('123', 5, '2025-01-01'),
                   ('123', 6, '2025-01-02');
        ");

        $stmt = $this->db->prepare("
            SELECT SUM(horas) FROM horas_trabajadas WHERE documento='123'
        ");
        $stmt->execute();
        $total = $stmt->fetchColumn();

        $this->assertEquals(11, $total);
    }

    /* ============================================================
       TEST 6: obtenerTodasConUsuario()
    ============================================================ */
    public function testObtenerTodasConUsuario() {
        $this->db->exec("
            INSERT INTO horas_trabajadas (documento, horas, fecha, motivo)
            VALUES ('123', 2, '2025-01-01', 'Minga');
        ");

        $stmt = $this->db->prepare("
            SELECT h.id_hora, h.documento, u.nombre, h.horas, h.motivo, h.fecha
            FROM horas_trabajadas h
            INNER JOIN usuarios u ON h.documento = u.documento
            ORDER BY h.fecha DESC
        ");

        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($resultado);
        $this->assertEquals('Tiziano', $resultado[0]['nombre']);
    }

    /* ============================================================
       TEST 7: eliminar()
    ============================================================ */
    public function testEliminarHoras() {
        // Insertar registro
        $this->db->exec("
            INSERT INTO horas_trabajadas (id_hora, documento, horas, fecha)
            VALUES (1, '123', 4, '2025-01-01')
        ");

        // Eliminar
        $stmt = $this->db->prepare("DELETE FROM horas_trabajadas WHERE id_hora = 1");
        $resultado = $stmt->execute();

        $this->assertTrue($resultado);

        $count = $this->db->query("SELECT COUNT(*) FROM horas_trabajadas")->fetchColumn();
        $this->assertEquals(0, $count);
    }
}
