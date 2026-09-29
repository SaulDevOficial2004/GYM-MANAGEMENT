<?php

require_once __DIR__ . '/../includes/api_auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    apiError('Método no permitido', 405);
}

requireApiRoles([
    'Administrador',
    'Dueño'
]);

header('Content-Type: application/json');

require_once "../php_action/conn_db.php";

//VALIDAR SESION

if(!isset($_SESSION['telefono'])){

    jsonResponse([
        "success"=>false,
        "message"=>"Sesión expirada."
    ]);

}

//VALIDAR DATOS

if(empty($_POST['id']) || empty(trim($_POST['banco'])) || empty(trim($_POST['titular'])) || empty(trim($_POST['clabe']))){

    jsonResponse([
        "success"=>false,
        "message"=>"Todos los campos son obligatorios."
    ]);

}

$id = intval($_POST['id']);
$banco = trim($_POST['banco']);
$titular = trim($_POST['titular']);
$clabe = trim($_POST['clabe']);

//ACTUALIZAR

$sql = "

    UPDATE configuracion_transferencias SET

        banco = ?,
        titular = ?,
        clabe = ?

    WHERE id = ?

";

$stmt = $connect->prepare($sql);
$stmt->bind_param("sssi",

    $banco,
    $titular,
    $clabe,
    $id

);

$connect->begin_transaction();

try{

if($stmt->execute()){

    require_once __DIR__ . '/../includes/audit.php';

    $usuario_id = apiCurrentUserId();

    if (!registerActivity(

        $connect,
        $usuario_id,
        'EDITAR',
        'CONFIGURACION_BANCARIA',
        1,
        'Actualizó la configuración bancaria y de transferencias'

    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    jsonResponse([

        "success"=>true,
        "message"=>"Datos bancarios actualizados correctamente."

    ]);

}else{

    $connect->rollback();

    jsonResponse([

        "success"=>false,
        "message"=>"No fue posible actualizar la configuración."

    ]);

}

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en update_transferencias: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

$stmt->close();
$connect->close();

?>