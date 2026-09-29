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

        tipo,
        COALESCE(SUM(total), 0) AS total

    FROM ventas

    WHERE tipo IN (
        'MEMBRESIA',
        'PRODUCTO',
        'VISITA',
        'TOALLA'
    )

    GROUP BY tipo

";

$result = $connect->query($sql);

if(!$result){

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible consultar la distribución de ingresos.'
    ]);

}

$totales = [

    'MEMBRESIA' => 0,
    'PRODUCTO' => 0,
    'VISITA' => 0,
    'TOALLA' => 0

];

while($row = $result->fetch_assoc()){

    $tipo = $row['tipo'];

    if(isset($totales[$tipo])){

        $totales[$tipo] =
            floatval($row['total']);

    }

}

jsonResponse([

    'success' => true,

    'data' => [

        [
            'tipo' => 'Membresías',
            'total' => $totales['MEMBRESIA']
        ],

        [
            'tipo' => 'Productos',
            'total' => $totales['PRODUCTO']
        ],

        [
            'tipo' => 'Visitas',
            'total' => $totales['VISITA']
        ],

        [
            'tipo' => 'Toallas',
            'total' => $totales['TOALLA']
        ]

    ]

]);

$connect->close();

?>