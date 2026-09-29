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

if(empty($_POST['id']) || empty($_POST['nombre']) || empty($_POST['precio']) || empty($_POST['dias'])){

    jsonResponse([

        'success' => false,
        'message' => 'Complete los campos obligatorios.'

    ]);

}

//VARIABLES

$id = intval($_POST['id']);
$nombre = trim($_POST['nombre']);
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = floatval($_POST['precio']);
$promocion = intval($_POST['promocion'] ?? 0);
$precio_promocion = null;
$dias = intval($_POST['dias']);

if($id <= 0 || $precio <= 0 || $dias <= 0){

    jsonResponse([

        'success' => false,
        'message' => 'Los datos de la membresía no son válidos.'

    ]);

}

//VALIDAR QUE EXISTA

$sqlMembresia = "

    SELECT id
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

if($resultMembresia->num_rows !== 1){

    $stmtMembresia->close();

    jsonResponse([

        'success' => false,
        'message' => 'La membresía no existe.'

    ]);

}

$stmtMembresia->close();

//VALIDAR PROMOCION

if($promocion === 1){

    if(empty($_POST['precio_promocion'])){

        jsonResponse([

            'success' => false,
            'message' => 'Debe ingresar el precio de promoción.'

        ]);

    }

    $precio_promocion = floatval($_POST['precio_promocion']);

    if($precio_promocion <= 0){

        jsonResponse([

            'success' => false,
            'message' => 'El precio de promoción no es válido.'

        ]);

    }

}

//UPDATE

$sql = "

    UPDATE membresias
    SET

        nombre = ?,
        descripcion = ?,
        precio = ?,
        promocion = ?,
        precio_promocion = ?,
        dias = ?

    WHERE id = ?

";

$stmt = $connect->prepare($sql);

if(!$stmt){

    jsonResponse([

        'success' => false,
        'message' => 'No fue posible preparar la actualización.'

    ]);

}

$stmt->bind_param(

    "ssdidii",

    $nombre,
    $descripcion,
    $precio,
    $promocion,
    $precio_promocion,
    $dias,
    $id

);

// TRANSACCION

$connect->begin_transaction();

try{

if($stmt->execute()){

    $usuario_id = apiCurrentUserId();

    if (!registerActivity(

        $connect,
        $usuario_id,
        'EDITAR',
        'MEMBRESIAS',
        $id,
        "Actualizó la membresía {$nombre}"

    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    jsonResponse([

        'success' => true,
        'message' => 'Membresía actualizada correctamente.'

    ]);

}else{

    $connect->rollback();

    jsonResponse([

        'success' => false,
        'message' => 'No fue posible actualizar la membresía.'

    ]);

}

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en update_membership_admin: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

$stmt->close();
$connect->close();

?>