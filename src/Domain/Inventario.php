<?php

declare(strict_types=1);

namespace GMS\Domain;

final class Inventario
{
    public static function puedeDescontar(int $stock, int $cantidad): bool
    {
        return $stock >= $cantidad;
    }

    public static function descontar(int $stock, int $cantidad): int
    {
        return $stock - $cantidad;
    }
}
