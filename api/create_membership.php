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

//VALIDACION

if(empty($_POST['nombre']) || empty($_POST['precio']) || empty($_POST['dias'])){

    jsonResponse([
        'success' => false,
        'message' => 'Completa los campos obligatorios.'
    ]);

}

$nombre = trim($_POST['nombre']);
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = floatval($_POST['precio']);
$promocion = intval($_POST['promocion'] ?? 0);
$precio_promocion = null;
$dias = intval($_POST['dias']);

//VALIDACIONES NUMERICAS

if($precio <= 0 || $dias <= 0){

    jsonResponse([
        'success' => false,
        'message' => 'El precio y los días deben ser mayores a cero.'
    ]);

}

//VALIDAR PROMOCION

if($promocion === 1){

    if(empty($_POST['precio_promocion'])){

        jsonResponse([
            'success' => false,
            'message' => 'Debe ingresar el precio de la promoción.'
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

//INSERT

$sql = "

    INSERT INTO membresias
    (
        nombre,
        descripcion,
        precio,
        promocion,
        precio_promocion,
        dias
    )
    VALUES
    (
        ?, ?, ?, ?, ?, ?
    )

";

$stmt = $connect->prepare($sql);

if(!$stmt){

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible preparar el registro de la membresía.'
    ]);

}

$stmt->bind_param(

    "ssdidi",

    $nombre,
    $descripcion,
    $precio,
    $promocion,
    $precio_promocion,
    $dias

);

//EJECUCION

$connect->begin_transaction();

try{

if($stmt->execute()){

    $membresia_id = $connect->insert_id;

    $usuario_id = apiCurrentUserId();

    if (!registerActivity(

        $connect,
        $usuario_id,
        'CREAR',
        'MEMBRESIAS',
        $membresia_id,
        "Registró la membresía {$nombre}"

    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    jsonResponse([

        'success' => true,
        'message' => 'Membresía registrada correctamente.'

    ]);

}else{

    $connect->rollback();

    jsonResponse([

        'success' => false,
        'message' => 'No fue posible registrar la membresía.'

    ]);

}

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en create_membership: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

$stmt->close();
$connect->close();

?>