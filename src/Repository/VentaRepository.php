<?php

declare(strict_types=1);

namespace GMS\Repository;

use mysqli;

class VentaRepository
{
    public function __construct(private mysqli $connect)
    {
    }

    public function visitasHoy(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM visitas
            WHERE DATE(fecha_visita) = CURDATE()
        ";

        $result = $this->connect->query($sql);

        return (int) $result->fetch_assoc()['total'];
    }

    public function ventasHoyTotal(): float
    {
        $sql = "
            SELECT
                IFNULL(SUM(total), 0) AS total
            FROM ventas
            WHERE DATE(fecha_venta) = CURDATE()
        ";

        $result = $this->connect->query($sql);

        return (float) $result->fetch_assoc()['total'];
    }
}
