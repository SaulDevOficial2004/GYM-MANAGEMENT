<?php

declare(strict_types=1);

namespace GMS\Repository;

use mysqli;

class VisitanteRepository
{
    public function __construct(private mysqli $connect)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function resumen(): array
    {
        $sql = "
            SELECT
                COUNT(*) AS total_visitas,
                COUNT(DISTINCT visitante_id) AS visitantes_unicos,
                SUM(DATE(fecha_visita) = CURDATE()) AS visitas_hoy,
                SUM(
                    YEAR(fecha_visita) = YEAR(CURDATE())
                    AND MONTH(fecha_visita) = MONTH(CURDATE())
                ) AS visitas_mes
            FROM visitas
        ";

        $result = $this->connect->query($sql);

        if (!$result) {
            return [];
        }

        $resumen = $result->fetch_assoc();

        return is_array($resumen) ? $resumen : [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listarHistorial(int $limite, int $offset): array
    {
        $sql = "
            SELECT
                visitantes.id,
                visitantes.nombre,
                visitas.fecha_visita
            FROM visitas
            INNER JOIN visitantes
                ON visitantes.id = visitas.visitante_id
            ORDER BY visitas.fecha_visita DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->connect->prepare($sql);
        $stmt->bind_param('ii', $limite, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        $visitas = [];

        while ($visita = $result->fetch_assoc()) {
            $visitas[] = $visita;
        }

        $stmt->close();

        return $visitas;
    }

    public function contar(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM visitas
        ";

        $result = $this->connect->query($sql);

        return (int) $result->fetch_assoc()['total'];
    }
}
