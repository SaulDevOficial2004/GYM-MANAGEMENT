<?php

use GMS\Domain\Inventario;
use PHPUnit\Framework\TestCase;

class InventarioTest extends TestCase
{
    public function test_descuento_normal(): void
    {
        $this->assertTrue(Inventario::puedeDescontar(10, 3));
        $this->assertSame(7, Inventario::descontar(10, 3));
    }

    public function test_descuento_exacto_deja_cero(): void
    {
        $this->assertTrue(Inventario::puedeDescontar(5, 5));
        $this->assertSame(0, Inventario::descontar(5, 5));
    }

    public function test_stock_insuficiente(): void
    {
        $this->assertFalse(Inventario::puedeDescontar(2, 5));
    }

    public function test_sin_stock(): void
    {
        $this->assertFalse(Inventario::puedeDescontar(0, 1));
    }
}
