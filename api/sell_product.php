<?php

require_once '../php_action/conn_db.php';

require_once __DIR__ . '/../includes/api_auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    apiError('Método no permitido', 405);
}

requireApiRoles([
    'Administrador',
    'Dueño',
    'Recepcionista'
]);

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$producto_id = $data['producto_id'];
$cantidad = $data['cantidad'];

require_once __DIR__ . '/../includes/audit.php';

// PRODUCTO (lectura previa sin bloqueo para mensajes iguales)

$sqlProducto = "
    SELECT *
    FROM productos
    WHERE id = ?
    AND activo = 1
";

$stmtProducto = $connect->prepare(
    $sqlProducto
);

$stmtProducto->bind_param(
    "i",
    $producto_id
);

$stmtProducto->execute();

$resultProducto =
    $stmtProducto->get_result();

$producto =
    $resultProducto->fetch_assoc();

if(!$producto){

    jsonResponse([
        "success" => false,
        "message" => "Producto no encontrado"
    ]);
}

// TRANSACCION

$connect->begin_transaction();

try{

    // STOCK CON BLOQUEO

    $sqlBloqueo = "
        SELECT stock, precio, nombre
        FROM productos
        WHERE id = ?
        FOR UPDATE
    ";

    $stmtBloqueo = $connect->prepare(
        $sqlBloqueo
    );

    $stmtBloqueo->bind_param(
        "i",
        $producto_id
    );

    $stmtBloqueo->execute();

    $resultBloqueo =
        $stmtBloqueo->get_result();

    $fila =
        $resultBloqueo->fetch_assoc();

    if(!$fila){

        $connect->rollback();

        jsonResponse([
            "success" => false,
            "message" => "Producto no encontrado"
        ]);
    }

    if(!\GMS\Domain\Inventario::puedeDescontar((int) $fila['stock'], (int) $cantidad)){

        $connect->rollback();

        jsonResponse([
            "success" => false,
            "message" => "Stock insuficiente"
        ]);
    }

    // DESCONTAR STOCK

    $nuevoStock =
        \GMS\Domain\Inventario::descontar((int) $fila['stock'], (int) $cantidad);

    $sqlStock = "
        UPDATE productos
        SET stock = ?
        WHERE id = ?
    ";

    $stmtStock = $connect->prepare(
        $sqlStock
    );

    $stmtStock->bind_param(
        "ii",
        $nuevoStock,
        $producto_id
    );

    $stmtStock->execute();

    // USUARIO RESPONSABLE

    $usuario_id = apiCurrentUserId();

    // TOTAL

    $total =
        $fila['precio'] * $cantidad;

    // VENTA

    $tipo = 'PRODUCTO';

    $descripcion =
        'Venta de ' .
        $fila['nombre'] .
        ' x' .
        $cantidad;

    $sqlVenta = "
        INSERT INTO ventas
        (
            usuario_id,
            tipo,
            descripcion,
            referencia_id,
            total
        )
        VALUES
        (
            ?, ?, ?, ?, ?
        )
    ";

    $stmtVenta = $connect->prepare(
        $sqlVenta
    );

    $stmtVenta->bind_param(

        "issid",

        $usuario_id,
        $tipo,
        $descripcion,
        $producto_id,
        $total

    );

    $stmtVenta->execute();

    $venta_id = $connect->insert_id;

    // BITACORA

    if (!registerActivity(

        $connect,
        $usuario_id,
        'CREAR',
        'VENTAS',
        $venta_id,
        $descripcion

    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    jsonResponse([

        "success" => true,

        "message" =>
        "Venta registrada correctamente"

    ]);

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en sell_product: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}