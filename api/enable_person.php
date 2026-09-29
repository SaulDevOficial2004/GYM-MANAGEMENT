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

//RECEPCIÓN DE DATOS

$data = json_decode(file_get_contents("php://input"), true);

//VALIDACION DE ID

if(!isset($data['id'])){

    jsonResponse([
        "status" => "error",
        "message" => "ID no recibida"
    ]);

}

$id = intval($data['id']);

if($id <= 0){

    jsonResponse([
        "status" => "error",
        "message" => "ID no válida"
    ]);

}

//OBTENER PERSONA

$sqlPersona = "

    SELECT

        id,
        nombre,
        estatus

    FROM personas
    WHERE id = ?
    LIMIT 1

";

$stmtPersona = $connect->prepare($sqlPersona);

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

$persona = $resultPersona->fetch_assoc();

$stmtPersona->close();

if((int) $persona['estatus'] === 1){

    jsonResponse([
        "status" => "error",
        "message" => "La persona ya está habilitada"
    ]);

}

//HABILITAR PERSONA

$sql = "

    UPDATE personas
    SET estatus = 1
    WHERE id = ?

";

$stmt = $connect->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

//RESPUESTA

$connect->begin_transaction();

try{

if($stmt->execute()){

    $usuario_id = apiCurrentUserId();

    if (!registerActivity(

        $connect,
        $usuario_id,
        'ACTIVAR',
        'PERSONAS',
        $id,
        "Habilitó al cliente {$persona['nombre']}"

    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    jsonResponse([

        "status" => "success",
        "message" => "Persona habilitada correctamente"

    ]);

}else{

    $connect->rollback();

    jsonResponse([

        "status" => "error",
        "message" => "Error al habilitar a la persona"
    ]);

}

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en enable_person: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

//CERRAR CONEXION

$stmt->close();
$connect->close();

?>