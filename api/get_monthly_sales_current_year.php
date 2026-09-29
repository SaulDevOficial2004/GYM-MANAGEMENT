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

        MONTH(created_at) AS mes,
        COALESCE(SUM(total), 0) AS total

    FROM ventas

    WHERE YEAR(created_at) = YEAR(CURDATE())

    GROUP BY MONTH(created_at)

    ORDER BY MONTH(created_at) ASC

";

$result = $connect->query($sql);

if(!$result){

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible consultar las ventas mensuales.'
    ]);

}

$ventasPorMes = [];

while($row = $result->fetch_assoc()){

    $ventasPorMes[
        intval($row['mes'])
    ] = floatval(
        $row['total']
    );

}

$datos = [];

for($mes = 1; $mes <= 12; $mes++){

    $datos[] = [

        'mes' => $mes,

        'total' =>
            $ventasPorMes[$mes]
            ?? 0

    ];

}

jsonResponse([

    'success' => true,

    'anio' => intval(
        date('Y')
    ),

    'data' => $datos

]);

$connect->close();

?>