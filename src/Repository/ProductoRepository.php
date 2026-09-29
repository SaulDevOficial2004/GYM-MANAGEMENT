<?php

declare(strict_types=1);

namespace GMS\Repository;

use mysqli;

class ProductoRepository
{
    public function __construct(private mysqli $connect)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listar(int $limite, int $offset): array
    {
        $sql = "
            SELECT
                id,
                nombre,
                descripcion,
                precio,
                stock,
                activo
            FROM productos
            ORDER BY
                activo DESC,
                nombre ASC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->connect->prepare($sql);
        $stmt->bind_param('ii', $limite, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        $productos = [];

        while ($producto = $result->fetch_assoc()) {
            $productos[] = $producto;
        }

        $stmt->close();

        return $productos;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function buscarPorId(int $id): ?array
    {
        $sql = "
            SELECT
                id,
                nombre,
                descripcion,
                precio,
                stock,
                activo
            FROM productos
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->connect->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();

        $stmt->close();

        return is_array($producto) ? $producto : null;
    }

    public function contar(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM productos
        ";

        $result = $this->connect->query($sql);

        return (int) $result->fetch_assoc()['total'];
    }

    /**
     * Mismas reglas que el conteo anterior en PHP.
     *
     * @return array{total:int, activos:int, inactivos:int, stock:int, stock_bajo:int, valor:float}
     */
    public function estadisticas(): array
    {
        $sql = "
            SELECT
                COUNT(*) AS total,
                SUM(activo = 1) AS activos,
                SUM(activo <> 1) AS inactivos,
                COALESCE(SUM(stock), 0) AS stock,
                SUM(stock <= 5) AS stock_bajo,
                COALESCE(SUM(precio * stock), 0) AS valor
            FROM productos
        ";

        $result = $this->connect->query($sql);
        $fila = $result->fetch_assoc();

        return [
            'total' => (int) $fila['total'],
            'activos' => (int) $fila['activos'],
            'inactivos' => (int) $fila['inactivos'],
            'stock' => (int) $fila['stock'],
            'stock_bajo' => (int) $fila['stock_bajo'],
            'valor' => (float) $fila['valor'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listarActivos(): array
    {
        $sql = "
            SELECT
                id,
                nombre,
                descripcion,
                precio,
                stock,
                activo
            FROM productos
            WHERE activo = 1
            ORDER BY nombre ASC
        ";

        $result = $this->connect->query($sql);
        $productos = [];

        while ($producto = $result->fetch_assoc()) {
            $productos[] = $producto;
        }

        return $productos;
    }
}
