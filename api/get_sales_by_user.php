<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../php_action/conn_db.php';
require_once __DIR__ . '/../includes/api_auth.php';

requireApiRoles([
    'Administrador',
    'Dueño'
]);

$periodo = trim(
    $_GET['periodo'] ?? 'month'
);

$periodosPermitidos = [
    'today',
    'month',
    'year',
    'all'
];

if(
    !in_array(
        $periodo,
        $periodosPermitidos,
        true
    )
){

    $periodo = 'month';

}

$condicionFecha = '';

switch($periodo){

    case 'today':

        $condicionFecha = "
            AND DATE(v.created_at) = CURDATE()
        ";

        break;

    case 'month':

        $condicionFecha = "
            AND MONTH(v.created_at) = MONTH(CURDATE())
            AND YEAR(v.created_at) = YEAR(CURDATE())
        ";

        break;

    case 'year':

        $condicionFecha = "
            AND YEAR(v.created_at) = YEAR(CURDATE())
        ";

        break;

    case 'all':

        $condicionFecha = '';

        break;

}

$sql = "

    SELECT

        u.id,
        u.nombre,

        COALESCE(
            SUM(v.total),
            0
        ) AS total_vendido,

        COUNT(v.id) AS operaciones,

        COALESCE(
            AVG(v.total),
            0
        ) AS promedio_operacion

    FROM usuarios u

    INNER JOIN ventas v
    ON v.usuario_id = u.id

    WHERE 1 = 1

    {$condicionFecha}

    GROUP BY

        u.id,
        u.nombre

    ORDER BY total_vendido DESC

";

$result = $connect->query($sql);

if(!$result){

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible consultar las ventas por usuario.'
    ]);

}

$usuarios = [];

$totalGeneral = 0;
$totalOperaciones = 0;

while(
    $row = $result->fetch_assoc()
){

    $totalVendido =
        floatval(
            $row['total_vendido']
        );

    $operaciones =
        intval(
            $row['operaciones']
        );

    $usuarios[] = [

        'id' =>
            intval($row['id']),

        'nombre' =>
            $row['nombre'],

        'total' =>
            $totalVendido,

        'operaciones' =>
            $operaciones,

        'promedio' =>
            floatval(
                $row['promedio_operacion']
            )

    ];

    $totalGeneral +=
        $totalVendido;

    $totalOperaciones +=
        $operaciones;

}

jsonResponse([

    'success' => true,

    'periodo' => $periodo,

    'total_general' =>
        $totalGeneral,

    'total_operaciones' =>
        $totalOperaciones,

    'data' =>
        $usuarios

]);

$connect->close();

?>