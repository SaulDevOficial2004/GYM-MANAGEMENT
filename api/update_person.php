<?php

require_once '../php_action/conn_db.php';

require_once __DIR__ . '/../includes/api_auth.php';
require_once __DIR__ . '/../includes/audit.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    apiError('Método no permitido', 405);
}

requireApiRoles([
    'Administrador',
    'Dueño',
    'Recepcionista'
]);

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

if(!is_array($data)){

    jsonResponse([
        "status" => "error",
        "message" => "No fue posible leer los datos enviados"
    ]);

}

$id = intval($data['id'] ?? 0);
$nombre = trim($data['nombre'] ?? '');

if($id <= 0 || $nombre === ''){

    jsonResponse([
        "status" => "error",
        "message" => "El ID y el nombre son obligatorios"
    ]);

}

/* OBTENER DATOS ACTUALES */

$sqlPersona = "

    SELECT

        id,
        nombre,
        folio,
        fecha_ini,
        fecha_fin

    FROM personas
    WHERE id = ?
    LIMIT 1

";

$stmtPersona = $connect->prepare($sqlPersona);

if(!$stmtPersona){

    jsonResponse([
        "status" => "error",
        "message" => "No fue posible validar a la persona"
    ]);

}

$stmtPersona->bind_param(
    "i",
    $id
);

$stmtPersona->execute();

$resultPersona = $stmtPersona->get_result();

if($resultPersona->num_rows !== 1){

    $stmtPersona->close();

    jsonResponse([
        "status" => "error",
        "message" => "La persona no existe"
    ]);

}

$personaActual = $resultPersona->fetch_assoc();

$stmtPersona->close();

/* CONSERVAR DATOS CUANDO NO SE ENVÍEN */

$folio = trim($data['folio'] ?? $personaActual['folio']);
$fecha_ini = $data['fecha_ini'] ?? $data['fechaInicio'] ?? $personaActual['fecha_ini'];

$fecha_fin = $data['fecha_fin'] ?? $data['fechaFin'] ?? $personaActual['fecha_fin'];

if($folio === '' || $fecha_ini === '' || $fecha_fin === ''){

    jsonResponse([
        "status" => "error",
        "message" => "Las fechas o el folio no son válidos"
    ]);

}

/* ACTUALIZAR PERSONA */

$sql = "

    UPDATE personas
    SET

        nombre = ?,
        folio = ?,
        fecha_ini = ?,
        fecha_fin = ?

    WHERE id = ?

";

$stmt = $connect->prepare($sql);

if(!$stmt){

    jsonResponse([
        "status" => "error",
        "message" => "No fue posible preparar la actualización"
    ]);

}

$stmt->bind_param(

    "ssssi",

    $nombre,
    $folio,
    $fecha_ini,
    $fecha_fin,
    $id

);

$connect->begin_transaction();

try{

if($stmt->execute()){

    $usuario_id = apiCurrentUserId();

    if (!registerActivity(

        $connect,
        $usuario_id,
        'EDITAR',
        'PERSONAS',
        $id,
        "Actualizó los datos del cliente {$nombre}"

    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    jsonResponse([
        "status" => "success",
        "message" => "Persona actualizada correctamente"
    ]);

}else{

    $connect->rollback();

    jsonResponse([
        "status" => "error",
        "message" => "Error al actualizar la persona"
    ]);

}

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en update_person: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

$stmt->close();
$connect->close();

?>