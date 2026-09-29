<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../php_action/conn_db.php';

require_once __DIR__ . '/../includes/api_auth.php';

requireApiRoles([
    'Administrador',
    'Dueño'
]);

$usuario_id = intval($_GET['usuario_id'] ?? 0);

$modulo = trim($_GET['modulo'] ?? '');
$accion = trim($_GET['accion'] ?? '');
$fecha_desde = trim($_GET['fecha_desde'] ?? '');
$fecha_hasta = trim($_GET['fecha_hasta'] ?? '');
$pagina = intval($_GET['pagina'] ?? 1);

$limite = 15;

if($pagina < 1){
    $pagina = 1;

}

$offset = ($pagina - 1) * $limite;


/* TOTAL DE RESULTADOS */

$sqlTotal = "

    SELECT COUNT(*) AS total
    FROM bitacora_actividad b
    INNER JOIN usuarios u
    ON u.id = b.usuario_id
    WHERE
        (? = 0 OR b.usuario_id = ?)

    AND
        (? = '' OR b.modulo = ?)

    AND
        (? = '' OR b.accion = ?)

    AND
        (? = '' OR DATE(b.fecha) >= ?)

    AND
        (? = '' OR DATE(b.fecha) <= ?)

";

$stmtTotal = $connect->prepare($sqlTotal);

$stmtTotal->bind_param(

    "iissssssss",

    $usuario_id,
    $usuario_id,

    $modulo,
    $modulo,

    $accion,
    $accion,

    $fecha_desde,
    $fecha_desde,

    $fecha_hasta,
    $fecha_hasta

);

$stmtTotal->execute();

$resultTotal = $stmtTotal->get_result();

$totalRegistros = intval($resultTotal->fetch_assoc()['total']);

$stmtTotal->close();

$totalPaginas = max(
    1,
    (int) ceil(
        $totalRegistros / $limite
    )
);

if($pagina > $totalPaginas){
    $pagina = $totalPaginas;

    $offset = ($pagina - 1) * $limite;

}


/* OBTENER REGISTROS */

$sql = "

    SELECT

        b.id,
        b.accion,
        b.modulo,
        b.registro_id,
        b.descripcion,
        b.motivo,
        b.fecha,

        u.nombre AS usuario_responsable

    FROM bitacora_actividad b
    INNER JOIN usuarios u
    ON u.id = b.usuario_id
    WHERE
        (? = 0 OR b.usuario_id = ?)

    AND
        (? = '' OR b.modulo = ?)

    AND
        (? = '' OR b.accion = ?)

    AND
        (? = '' OR DATE(b.fecha) >= ?)

    AND
        (? = '' OR DATE(b.fecha) <= ?)

    ORDER BY b.fecha DESC

    LIMIT ?

    OFFSET ?

";

$stmt = $connect->prepare($sql);

$stmt->bind_param(

    "iissssssssii",

    $usuario_id,
    $usuario_id,

    $modulo,
    $modulo,

    $accion,
    $accion,

    $fecha_desde,
    $fecha_desde,

    $fecha_hasta,
    $fecha_hasta,

    $limite,
    $offset

);

$stmt->execute();

$result = $stmt->get_result();

$registros = [];

while(
    $row = $result->fetch_assoc()
){

    $registros[] = [

        'id' => intval($row['id']),
        'usuario' => $row['usuario_responsable'],
        'accion' => $row['accion'],
        'modulo' => $row['modulo'],
        'registro_id' => $row['registro_id'] !== null ? intval($row['registro_id']) : null,
        'descripcion' => $row['descripcion'],
        'motivo' => $row['motivo'],
        'fecha' => date('d/m/Y h:i A', strtotime($row['fecha']))

    ];

}

$stmt->close();
$connect->close();

jsonResponse([

    'success' => true,
    'data' => $registros,

    'pagination' => [

        'pagina_actual' => $pagina,
        'total_paginas' => $totalPaginas,
        'total_registros' => $totalRegistros,
        'limite' => $limite

    ]

]);