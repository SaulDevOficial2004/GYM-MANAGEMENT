<?php

require_once __DIR__ . '/../php_action/conn_db.php';
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

$nombre = trim($_POST['nombre']);
$descripcion = trim($_POST['descripcion']);
$precio = $_POST['precio'];
$stock = $_POST['stock'];

$sql = "
    INSERT INTO productos
    (
        nombre,
        descripcion,
        precio,
        stock
    )
    VALUES
    (
        ?,?,?,?
    )
";

$stmt = $connect->prepare($sql);

$stmt->bind_param(
    "ssdi",
    $nombre,
    $descripcion,
    $precio,
    $stock
);

$connect->begin_transaction();

try{

if($stmt->execute()){

    $producto_id = $connect->insert_id;
    $usuario_id = apiCurrentUserId();

    if (!registerActivity(
        $connect,
        $usuario_id,
        'CREAR',
        'PRODUCTOS',
        $producto_id,
        "Registró el producto {$nombre}"
    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    jsonResponse([
        "success" => true,
        "message" => "Producto registrado correctamente"
    ]);

}else{

    $connect->rollback();

    jsonResponse([
        "success" => false,
        "message" => "Error al registrar producto"
    ]);

}

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en create_product: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}