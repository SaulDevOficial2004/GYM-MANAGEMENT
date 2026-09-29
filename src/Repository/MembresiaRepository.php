<?php

declare(strict_types=1);

namespace GMS\Repository;

use mysqli;

class MembresiaRepository
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
                promocion,
                precio_promocion,
                dias,
                activo
            FROM membresias
            ORDER BY
                activo DESC,
                dias ASC,
                nombre ASC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->connect->prepare($sql);
        $stmt->bind_param('ii', $limite, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        $membresias = [];

        while ($membresia = $result->fetch_assoc()) {
            $membresias[] = $membresia;
        }

        $stmt->close();

        return $membresias;
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
                promocion,
                precio_promocion,
                dias,
                activo
            FROM membresias
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->connect->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $membresia = $result->fetch_assoc();

        $stmt->close();

        return is_array($membresia) ? $membresia : null;
    }

    public function contar(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM membresias
        ";

        $result = $this->connect->query($sql);

        return (int) $result->fetch_assoc()['total'];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listarActivas(): array
    {
        $sql = "
            SELECT
                id,
                nombre,
                descripcion,
                precio,
                promocion,
                precio_promocion,
                dias,
                activo
            FROM membresias
            WHERE activo = 1
            ORDER BY
                dias ASC,
                nombre ASC
        ";

        $result = $this->connect->query($sql);
        $membresias = [];

        while ($membresia = $result->fetch_assoc()) {
            $membresias[] = $membresia;
        }

        return $membresias;
    }
}
