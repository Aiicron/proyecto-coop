<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Modelos/Comprobante.php';

//
// Clase testable (adaptada para PDO + SQLite)
//
class ComprobanteTestable extends Comprobante {
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Métodos adaptados para PDO (en vez de mysqli)
    public function query($sql) {
        return $this->db->query($sql);
    }
}

class ComprobanteTest extends TestCase {

    private $db;
    private $comprobante;

    protected function setUp(): void {
        // Crear BD en memoria
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crear tabla usuarios
        $this->db->exec("
            CREATE TABLE usuarios (
                documento TEXT PRIMARY KEY,
                nombre TEXT
            );
        ");

        // Tabla comprobantes
        $this->db->exec("
            CREATE TABLE comprobante_pago (
                id_comprobante INTEGER PRIMARY KEY AUTOINCREMENT,
                documento TEXT,
                tipo TEXT,
                archivo_pdf TEXT,
                estado TEXT,
                fecha_subida TEXT
            );
        ");

        // Insertar usuario de prueba
        $this->db->exec("INSERT INTO usuarios (documento, nombre) VALUES ('123', 'Tiziano');");

        // Insertar comprobante base
        $this->db->exec("
            INSERT INTO comprobante_pago (documento, tipo, archivo_pdf, estado)
            VALUES ('123','inicial','uploads/prueba.pdf','pendiente');
        ");

        $this->comprobante = new ComprobanteTestable($this->db);
    }

    /* ============================================================
       TEST 1: obtenerTodosConUsuario()
    ============================================================ */
    public function testObtenerTodosConUsuario() {
        $resultado = $this->db->query("
            SELECT c.id_comprobante, c.documento, u.nombre, c.archivo_pdf, c.estado, c.tipo
            FROM comprobante_pago c
            INNER JOIN usuarios u ON c.documento = u.documento
            ORDER BY c.id_comprobante DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($resultado);
        $this->assertEquals('123', $resultado[0]['documento']);
        $this->assertEquals('Tiziano', $resultado[0]['nombre']);
    }

    /* ============================================================
       TEST 2: aprobar()
    ============================================================ */
    public function testAprobarComprobante() {
        $this->db->exec("UPDATE comprobante_pago SET estado='pendiente'");

        $this->db->exec("UPDATE comprobante_pago SET estado='aprobado' WHERE id_comprobante=1");

        $estado = $this->db->query("SELECT estado FROM comprobante_pago WHERE id_comprobante=1")
                           ->fetchColumn();

        $this->assertEquals('aprobado', $estado);
    }

    /* ============================================================
       TEST 3: rechazar()
    ============================================================ */
    public function testRechazarComprobante() {
        $this->db->exec("UPDATE comprobante_pago SET estado='pendiente'");

        $this->db->exec("UPDATE comprobante_pago SET estado='rechazado' WHERE id_comprobante=1");

        $estado = $this->db->query("SELECT estado FROM comprobante_pago WHERE id_comprobante=1")
                           ->fetchColumn();

        $this->assertEquals('rechazado', $estado);
    }

    /* ============================================================
       TEST 4: obtenerDocumentoPorId()
    ============================================================ */
    public function testObtenerDocumentoPorId() {
        $doc = $this->db->query("SELECT documento FROM comprobante_pago WHERE id_comprobante=1")
                        ->fetchColumn();

        $this->assertEquals('123', $doc);
    }

    /* ============================================================
       TEST 5: tieneComprobanteInicialAprobado()
    ============================================================ */
    public function testTieneComprobanteInicialAprobado() {
        // Poner comprobante en estado aprobado
        $this->db->exec("UPDATE comprobante_pago SET estado='aprobado' WHERE id_comprobante=1");

        $resultado = ($this->db->query("
            SELECT * FROM comprobante_pago 
            WHERE documento='123' AND tipo='inicial' AND estado='aprobado' LIMIT 1
        ")->fetch() !== false);

        $this->assertTrue($resultado);
    }

    /* ============================================================
       TEST 6: obtenerEstado()
    ============================================================ */
    public function testObtenerEstado() {
        $estado = $this->db->query("
            SELECT estado FROM comprobante_pago 
            WHERE documento='123' AND tipo='inicial'
            ORDER BY id_comprobante DESC LIMIT 1
        ")->fetchColumn();

        $this->assertEquals('pendiente', $estado);
    }

    /* ============================================================
       TEST 7: obtenerHistorialMensuales()
    ============================================================ */
    public function testObtenerHistorialMensuales() {

        // Insertar comprobantes mensuales
        $this->db->exec("
            INSERT INTO comprobante_pago (documento, tipo, archivo_pdf, estado, fecha_subida)
            VALUES ('123','mensual','uploads/m1.pdf','pendiente', '2025-01-01'),
                   ('123','mensual','uploads/m2.pdf','aprobado', '2025-02-01');
        ");

        $resultado = $this->db->query("
            SELECT archivo_pdf, estado, fecha_subida 
            FROM comprobante_pago 
            WHERE documento='123' AND tipo='mensual'
            ORDER BY fecha_subida DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(2, $resultado);
        $this->assertEquals('uploads/m2.pdf', $resultado[0]['archivo_pdf']);
    }

    /* ============================================================
       TEST 8: obtenerUltimosComprobantes()
    ============================================================ */
    public function testObtenerUltimosComprobantes() {
        // Insertar más comprobantes
        $this->db->exec("
            INSERT INTO comprobante_pago (documento, tipo, archivo_pdf, estado, fecha_subida)
            VALUES ('123','mensual','uploads/x1.pdf','pendiente', '2025-01-01'),
                   ('123','mensual','uploads/x2.pdf','pendiente', '2025-02-01'),
                   ('123','mensual','uploads/x3.pdf','pendiente', '2025-03-01');
        ");

        $resultado = $this->db->query("
            SELECT tipo, estado, fecha_subida, archivo_pdf 
            FROM comprobante_pago
            WHERE documento='123'
            ORDER BY fecha_subida DESC
            LIMIT 3
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(3, $resultado);
        $this->assertEquals('uploads/x3.pdf', $resultado[0]['archivo_pdf']);
    }
}
