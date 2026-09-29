<?php

use GMS\Domain\MembresiaRenovacion;
use PHPUnit\Framework\TestCase;

class MembresiaTest extends TestCase
{
    public function test_renovacion_anticipada_extiende_desde_vencimiento(): void
    {
        $hoy = new DateTimeImmutable('2026-09-01');

        $resultado = MembresiaRenovacion::calcular('2026-09-20', 30, $hoy);

        $this->assertSame('2026-09-20', $resultado['inicio']);
        $this->assertSame('2026-10-19', $resultado['fin']);
    }

    public function test_renovacion_vencida_parte_de_hoy(): void
    {
        $hoy = new DateTimeImmutable('2026-09-01');

        $resultado = MembresiaRenovacion::calcular('2026-08-10', 30, $hoy);

        $this->assertSame('2026-09-01', $resultado['inicio']);
        $this->assertSame('2026-09-30', $resultado['fin']);
    }

    public function test_renovacion_el_mismo_dia_parte_de_hoy(): void
    {
        $hoy = new DateTimeImmutable('2026-09-01 12:00:00');

        $resultado = MembresiaRenovacion::calcular('2026-09-01', 7, $hoy);

        $this->assertSame('2026-09-01', $resultado['inicio']);
        $this->assertSame('2026-09-07', $resultado['fin']);
    }

    public function test_membresia_de_un_dia(): void
    {
        $hoy = new DateTimeImmutable('2026-09-01');

        $resultado = MembresiaRenovacion::calcular('2026-08-01', 1, $hoy);

        $this->assertSame('2026-09-01', $resultado['inicio']);
        $this->assertSame('2026-09-01', $resultado['fin']);
    }
}
