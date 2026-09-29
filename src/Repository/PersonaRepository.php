<?php

declare(strict_types=1);

namespace GMS\Repository;

use mysqli;

class PersonaRepository
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
                folio,
                fecha_ini,
                fecha_fin,
                membresia_id,
                estatus
            FROM personas
            ORDER BY
                estatus ASC,
                nombre ASC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->connect->prepare($sql);
        $stmt->bind_param('ii', $limite, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        $personas = [];

        while ($persona = $result->fetch_assoc()) {
            $personas[] = $persona;
        }

        $stmt->close();

        return $personas;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function buscarPorFolio(string $folio): ?array
    {
        $sql = "
            SELECT
                id,
                nombre,
                folio,
                fecha_ini,
                fecha_fin,
                membresia_id,
                estatus
            FROM personas
            WHERE folio = ?
            LIMIT 1
        ";

        $stmt = $this->connect->prepare($sql);
        $stmt->bind_param('s', $folio);
        $stmt->execute();

        $result = $stmt->get_result();
        $persona = $result->fetch_assoc();

        $stmt->close();

        return is_array($persona) ? $persona : null;
    }

    public function contar(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM personas
        ";

        $result = $this->connect->query($sql);

        return (int) $result->fetch_assoc()['total'];
    }

    /**
     * Mismo predicado que el dashboard: estatus activo y fecha
     * de vencimiento anterior al momento actual.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listarVencidas(): array
    {
        $sql = "
            SELECT
                id,
                nombre,
                fecha_fin
            FROM personas
            WHERE fecha_fin < NOW()
            AND estatus = 1
            ORDER BY fecha_fin ASC
        ";

        $result = $this->connect->query($sql);
        $personas = [];

        while ($persona = $result->fetch_assoc()) {
            $personas[] = $persona;
        }

        return $personas;
    }

    /**
     * Mismas reglas que el conteo anterior en PHP: vencida si
     * fecha_fin <= hoy, por vencer si hoy < fecha_fin <= hoy+7.
     *
     * @return array{total:int, activas:int, inhabilitadas:int, vencidas:int, por_vencer:int}
     */
    public function estadisticas(): array
    {
        $sql = "
            SELECT
                COUNT(*) AS total,
                SUM(estatus = 1) AS activas,
                SUM(estatus <> 1) AS inhabilitadas,
                SUM(estatus = 1 AND fecha_fin <= CURDATE()) AS vencidas,
                SUM(
                    estatus = 1
                    AND fecha_fin > CURDATE()
                    AND fecha_fin <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                ) AS por_vencer
            FROM personas
        ";

        $result = $this->connect->query($sql);
        $fila = $result->fetch_assoc();

        return [
            'total' => (int) $fila['total'],
            'activas' => (int) $fila['activas'],
            'inhabilitadas' => (int) $fila['inhabilitadas'],
            'vencidas' => (int) $fila['vencidas'],
            'por_vencer' => (int) $fila['por_vencer'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listarVencimientosProximos(int $dias = 7): array
    {
        $sql = "
            SELECT
                id,
                nombre,
                folio,
                fecha_fin
            FROM personas
            WHERE estatus = 1
              AND fecha_fin BETWEEN CURDATE()
              AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
            ORDER BY fecha_fin ASC
        ";

        $stmt = $this->connect->prepare($sql);
        $stmt->bind_param('i', $dias);
        $stmt->execute();

        $result = $stmt->get_result();
        $personas = [];

        while ($persona = $result->fetch_assoc()) {
            $personas[] = $persona;
        }

        $stmt->close();

        return $personas;
    }
}
