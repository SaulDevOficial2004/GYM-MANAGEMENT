<?php

declare(strict_types=1);

namespace GMS\Repository;

use mysqli;

class ReporteRepository
{
    private const PERIODOS = ['hoy', 'mes', 'anio', 'todo'];
    private const TIPOS = ['MEMBRESIA', 'PRODUCTO', 'VISITA', 'TOALLA'];

    public function __construct(private mysqli $connect)
    {
    }

    private function condicionPeriodo(string $periodo): string
    {
        if (!in_array($periodo, self::PERIODOS, true)) {
            $periodo = 'todo';
        }

        return match ($periodo) {
            'hoy' => 'AND DATE(created_at) = CURDATE()',
            'mes' => 'AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())',
            'anio' => 'AND YEAR(created_at) = YEAR(CURDATE())',
            default => '',
        };
    }

    public function totalPorPeriodo(string $periodo): float
    {
        $sql = "
            SELECT COALESCE(SUM(total), 0) AS total
            FROM ventas
            WHERE 1 = 1
            {$this->condicionPeriodo($periodo)}
        ";

        $result = $this->connect->query($sql);

        return (float) $result->fetch_assoc()['total'];
    }

    public function totalPorTipoPeriodo(string $tipo, string $periodo): float
    {
        if (!in_array($tipo, self::TIPOS, true)) {
            return 0.0;
        }

        $sql = "
            SELECT COALESCE(SUM(total), 0) AS total
            FROM ventas
            WHERE tipo = ?
            {$this->condicionPeriodo($periodo)}
        ";

        $stmt = $this->connect->prepare($sql);
        $stmt->bind_param('s', $tipo);
        $stmt->execute();

        $result = $stmt->get_result();
        $total = (float) $result->fetch_assoc()['total'];

        $stmt->close();

        return $total;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function usuariosBitacora(): array
    {
        $sql = "
            SELECT DISTINCT
                u.id,
                u.nombre
            FROM usuarios u
            INNER JOIN bitacora_actividad b
            ON b.usuario_id = u.id
            ORDER BY u.nombre ASC
        ";

        $result = $this->connect->query($sql);
        $usuarios = [];

        while ($usuario = $result->fetch_assoc()) {
            $usuarios[] = $usuario;
        }

        return $usuarios;
    }

    /**
     * @return array<int, string>
     */
    public function modulosBitacora(): array
    {
        $sql = "
            SELECT DISTINCT modulo
            FROM bitacora_actividad
            ORDER BY modulo ASC
        ";

        $result = $this->connect->query($sql);
        $modulos = [];

        while ($fila = $result->fetch_assoc()) {
            $modulos[] = (string) $fila['modulo'];
        }

        return $modulos;
    }

    /**
     * @return array<int, string>
     */
    public function accionesBitacora(): array
    {
        $sql = "
            SELECT DISTINCT accion
            FROM bitacora_actividad
            ORDER BY accion ASC
        ";

        $result = $this->connect->query($sql);
        $acciones = [];

        while ($fila = $result->fetch_assoc()) {
            $acciones[] = (string) $fila['accion'];
        }

        return $acciones;
    }
}
