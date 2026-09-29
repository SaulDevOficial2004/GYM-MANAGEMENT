<?php

declare(strict_types=1);

namespace GMS\Repository;

use mysqli;

class TransferenciaRepository
{
    public function __construct(private mysqli $connect)
    {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function obtenerConfiguracion(): ?array
    {
        $sql = "
            SELECT
                id,
                banco,
                clabe,
                titular
            FROM configuracion_transferencias
            LIMIT 1
        ";

        $result = $this->connect->query($sql);
        $configuracion = $result->fetch_assoc();

        return is_array($configuracion) ? $configuracion : null;
    }
}
