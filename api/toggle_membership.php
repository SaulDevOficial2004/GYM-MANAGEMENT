<?php

require_once __DIR__ . '/../includes/api_auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    apiError('Método no permitido', 405);
}

requireApiRoles([
    'Administrador',
    'Dueño',
    'Recepcionista'
]);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../php_action/conn_db.php';
require_once __DIR__ . '/../includes/audit.php';

//VALIDACIONES

if(empty($_POST['id']) || !isset($_POST['estado'])){

    jsonResponse([

        'success' => false,
        'message' => 'Datos incompletos.'

    ]);

}

$id = intval($_POST['id']);
$estado = intval($_POST['estado']);

//VALIDAR ESTADO

if($id <= 0 || ($estado !== 0 && $estado !== 1)){

    jsonResponse([

        'success' => false,
        'message' => 'Estado o membresía inválidos.'

    ]);

}

//OBTENER MEMBRESIA

$sqlMembresia = "

    SELECT

        id,
        nombre,
        activo

    FROM membresias
    WHERE id = ?
    LIMIT 1

";

$stmtMembresia = $connect->prepare($sqlMembresia);
$stmtMembresia->bind_param(
    "i",
    $id
);

$stmtMembresia->execute();
$resultMembresia = $stmtMembresia->get_result();
$membresia = $resultMembresia->fetch_assoc();
$stmtMembresia->close();

if(!$membresia){

    jsonResponse([

        'success' => false,
        'message' => 'La membresía no existe.'

    ]);

}

if((int) $membresia['activo'] === $estado){

    $mensaje = $estado === 1
        ? 'La membresía ya está activa.'
        : 'La membresía ya está desactivada.';

    jsonResponse([

        'success' => false,
        'message' => $mensaje

    ]);

}

//UPDATE

$sql = "

    UPDATE membresias
    SET activo = ?
    WHERE id = ?

";

$stmt = $connect->prepare($sql);

$stmt->bind_param(

    "ii",

    $estado,
    $id

);

$connect->begin_transaction();

try{

if($stmt->execute()){

    $usuario_id = apiCurrentUserId();

    $accion = $estado === 1
        ? 'ACTIVAR'
        : 'DESACTIVAR';

    $descripcion = $estado === 1
        ? "Activó la membresía {$membresia['nombre']}"
        : "Desactivó la membresía {$membresia['nombre']}";

    if (!registerActivity(

        $connect,
        $usuario_id,
        $accion,
        'MEMBRESIAS',
        $id,
        $descripcion

    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    $mensaje = $estado === 1
        ? 'Membresía activada correctamente.'
        : 'Membresía desactivada correctamente.';

    jsonResponse([

        'success' => true,
        'message' => $mensaje

    ]);

}else{

    $connect->rollback();

    jsonResponse([

        'success' => false,
        'message' => 'No fue posible actualizar el estado.'

    ]);

}

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en toggle_membership: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

$stmt->close();
$connect->close();

?>