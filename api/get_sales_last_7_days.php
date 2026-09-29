<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../php_action/conn_db.php';
require_once __DIR__ . '/../includes/api_auth.php';

requireApiRoles([
    'Administrador',
    'Dueño'
]);

$sql = "

    SELECT

        DATE(created_at) AS fecha,
        COALESCE(SUM(total), 0) AS total

    FROM ventas

    WHERE created_at >= DATE_SUB(
        CURDATE(),
        INTERVAL 6 DAY
    )

    AND created_at < DATE_ADD(
        CURDATE(),
        INTERVAL 1 DAY
    )

    GROUP BY DATE(created_at)

    ORDER BY DATE(created_at) ASC

";

$result = $connect->query($sql);

if(!$result){

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible consultar las ventas.'
    ]);

}

$ventasPorFecha = [];

while($row = $result->fetch_assoc()){

    $ventasPorFecha[$row['fecha']] = floatval($row['total']);

}

$datos = [];

for($diasAtras = 6; $diasAtras >= 0; $diasAtras--){

    $fecha = date('Y-m-d', strtotime("-{$diasAtras} days"));

    $datos[] = [

        'fecha' => $fecha,

        'total' => $ventasPorFecha[$fecha] ?? 0

    ];

}

jsonResponse([

    'success' => true,
    'data' => $datos

]);

$connect->close();

?>