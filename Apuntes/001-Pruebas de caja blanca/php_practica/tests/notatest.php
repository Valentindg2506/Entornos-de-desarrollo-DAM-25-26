<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/nota.php';
class NotaTest extends TestCase {
    public function testNotaNegativa() {
        $this->assertEquals("Nota no válida", evaluarNota(-1));
    }
    public function testSobresaliente() {
        $this->assertEquals("Sobresaliente", evaluarNota(9));
    }
    public function testNotable() {
        $this->assertEquals("Notable", evaluarNota(7));
    }
    public function testAprobado() {
        $this->assertEquals("Aprobado", evaluarNota(5));
    }
    public function testSuspenso() {
        // ESTE TEST FALLA
        $this->assertEquals("Suspenso", evaluarNota(3));
    }
}

