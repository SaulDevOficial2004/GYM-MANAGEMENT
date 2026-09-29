<?php

declare(strict_types=1);

namespace GMS\Repository;

use mysqli;

class ComprobanteRepository
{
    private const ESTATUS = ['PENDIENTE', 'CONFIRMADO', 'RECHAZADO'];

    public function __construct(private mysqli $connect)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listarPorEstatus(string $estatus): array
    {
        if (!in_array($estatus, self::ESTATUS, true)) {
            return [];
        }

        $sql = "
            SELECT
                cp.id,
                cp.persona_id,
                cp.folio_cliente,
                cp.concepto,
                cp.status,
                cp.fecha_subida,
                cp.archivo,
                cp.motivo_rechazo,
                p.nombre,
                m.nombre AS membresia,
                m.dias
            FROM comprobantes_pago cp
            INNER JOIN personas p
                ON p.id = cp.persona_id
            LEFT JOIN membresias m
                ON m.id = p.membresia_id
            WHERE cp.status = ?
            ORDER BY cp.fecha_subida ASC
        ";

        $stmt = $this->connect->prepare($sql);
        $stmt->bind_param('s', $estatus);
        $stmt->execute();

        $result = $stmt->get_result();
        $comprobantes = [];

        while ($comprobante = $result->fetch_assoc()) {
            $comprobantes[] = $comprobante;
        }

        $stmt->close();

        return $comprobantes;
    }

    public function contarPorEstatus(string $estatus): int
    {
        if (!in_array($estatus, self::ESTATUS, true)) {
            return 0;
        }

        $sql = "
            SELECT COUNT(*) AS total
            FROM comprobantes_pago
            WHERE status = ?
        ";

        $stmt = $this->connect->prepare($sql);
        $stmt->bind_param('s', $estatus);
        $stmt->execute();

        $result = $stmt->get_result();
        $total = (int) $result->fetch_assoc()['total'];

        $stmt->close();

        return $total;
    }
}
