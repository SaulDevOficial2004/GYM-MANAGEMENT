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
$estado = intval($_POST['estado']);

if($id <= 0 || !in_array($estado, [0, 1], true)){

    jsonResponse([
        "success" => false,
        "message" => "Los datos enviados no son válidos"
    ]);

}

//OBTENER PRODUCTO

$sqlProducto = "
    SELECT
        id,
        nombre,
        activo
    FROM productos
    WHERE id = ?
    LIMIT 1
";

$stmtProducto = $connect->prepare($sqlProducto);

$stmtProducto->bind_param(
    "i",
    $id
);

$stmtProducto->execute();

$resultProducto = $stmtProducto->get_result();

$producto = $resultProducto->fetch_assoc();

$stmtProducto->close();

if(!$producto){

    jsonResponse([
        "success" => false,
        "message" => "El producto no existe"
    ]);

}

//ACTUALIZAR ESTADO

$sql = "
    UPDATE productos
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

    $accion = $estado === 1 ? 'ACTIVAR' : 'DESACTIVAR';

    $descripcion = $estado === 1 ? "Activó el producto {$producto['nombre']}" : "Desactivó el producto {$producto['nombre']}";

    if (!registerActivity(
        $connect,
        $usuario_id,
        $accion,
        'PRODUCTOS',
        $id,
        $descripcion
    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    jsonResponse([
        "success" => true,
        "message" => "Estado actualizado"
    ]);

}else{

    $connect->rollback();

    jsonResponse([
        "success" => false,
        "message" => "No fue posible actualizar el estado"
    ]);

}

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en toggle_product: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}