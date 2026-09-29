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

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data['id'] ?? 0);

if($id <= 0){

    jsonResponse([
        "status" => "error",
        "message" => "Persona inválida"
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

if((int) $persona['estatus'] === 2){

    jsonResponse([
        "status" => "error",
        "message" => "La persona ya está inhabilitada"
    ]);

}

$sql = "

    UPDATE personas
    SET estatus = 2
    WHERE id = ?

";

$stmt = $connect->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$connect->begin_transaction();

try{

if($stmt->execute()){

    $usuario_id = apiCurrentUserId();

    if (!registerActivity(

        $connect,
        $usuario_id,
        'DESACTIVAR',
        'PERSONAS',
        $id,
        "Inhabilitó al cliente {$persona['nombre']} desde el módulo de personas"

    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    jsonResponse([
        "status" => "success",
        "message" => "Persona eliminada"
    ]);

}else{

    $connect->rollback();

    jsonResponse([
        "status" => "error",
        "message" => "Error al eliminar"
    ]);

}

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en delete_person: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

$stmt->close();

$connect->close();

?>