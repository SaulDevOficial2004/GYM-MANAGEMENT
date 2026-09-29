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

//=======================================
// VALIDAR ID
//=======================================

if(empty($_POST['id'])){

    jsonResponse([
        "success" => false,
        "message" => "Cliente inválido."
    ]);

}

$id = intval($_POST['id']);

if($id <= 0){

    jsonResponse([
        "success" => false,
        "message" => "Cliente inválido."
    ]);

}

//=======================================
// OBTENER CLIENTE
//=======================================

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
        "success" => false,
        "message" => "El cliente no existe."
    ]);

}

$persona = $resultPersona->fetch_assoc();

$stmtPersona->close();

if((int) $persona['estatus'] === 2){

    jsonResponse([
        "success" => false,
        "message" => "El cliente ya está inhabilitado."
    ]);

}

//=======================================
// INHABILITAR
//=======================================

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
        "Inhabilitó al cliente {$persona['nombre']} desde el dashboard"

    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    jsonResponse([

        "success" => true,
        "message" => "Cliente inhabilitado correctamente."

    ]);

}else{

    $connect->rollback();

    jsonResponse([

        "success" => false,
        "message" => "No fue posible inhabilitar al cliente."

    ]);

}

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en disable_member: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

$stmt->close();

$connect->close();

?>