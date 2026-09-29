<?php

declare(strict_types=1);

namespace GMS\Domain;

use DateTimeImmutable;

final class MembresiaRenovacion
{
    /**
     * @return array{inicio:string, fin:string} Fechas en formato Y-m-d.
     */
    public static function calcular(string $fechaFinActual, int $dias, ?DateTimeImmutable $hoy = null): array
    {
        $hoy = $hoy ?? new DateTimeImmutable();
        $finActual = new DateTimeImmutable($fechaFinActual);

        $inicio = $finActual > $hoy ? $finActual : $hoy;
        $fin = $inicio->modify('+' . ($dias - 1) . ' days');

        return [
            'inicio' => $inicio->format('Y-m-d'),
            'fin' => $fin->format('Y-m-d'),
        ];
    }
}
