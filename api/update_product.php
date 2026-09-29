<?php

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

require_once __DIR__ . '/../php_action/conn_db.php';

$id = intval($_POST['id']);

$nombre = trim($_POST['nombre']);
$descripcion = trim($_POST['descripcion']);
$precio = $_POST['precio'];
$stock = intval($_POST['stock']);

$sql = "
    UPDATE productos
    SET
        nombre = ?,
        descripcion = ?,
        precio = ?,
        stock = ?
    WHERE id = ?
";

$stmt = $connect->prepare($sql);

$stmt->bind_param(
    "ssdii",
    $nombre,
    $descripcion,
    $precio,
    $stock,
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
        'PRODUCTOS',
        $id,
        "Actualizó el producto {$nombre}"
    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    jsonResponse([
        "success" => true,
        "message" => "Producto actualizado"
    ]);

}else{

    $connect->rollback();

    jsonResponse([
        "success" => false,
        "message" => "Error al actualizar"
    ]);

}

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en update_product: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}