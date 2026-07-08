<?php

session_start();

header('Content-Type: application/json');

require_once '../php_action/conn_db.php';

if(!isset($_SESSION['telefono'])){

    echo json_encode([
        "success" => false,
        "message" => "Sesión expirada"
    ]);

    exit();
}

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$producto_id = $data['producto_id'];
$cantidad = $data['cantidad'];

// PRODUCTO

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

    echo json_encode([
        "success" => false,
        "message" => "Producto no encontrado"
    ]);

    exit();
}

// STOCK

if($producto['stock'] < $cantidad){

    echo json_encode([
        "success" => false,
        "message" => "Stock insuficiente"
    ]);

    exit();
}

// DESCONTAR STOCK

$nuevoStock =
    $producto['stock'] - $cantidad;

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

// USUARIO

$telefono = $_SESSION['telefono'];

$sqlUsuario = "
    SELECT id
    FROM usuarios
    WHERE telefono = ?
";

$stmtUsuario = $connect->prepare(
    $sqlUsuario
);

$stmtUsuario->bind_param(
    "s",
    $telefono
);

$stmtUsuario->execute();

$resultUsuario =
    $stmtUsuario->get_result();

$usuario =
    $resultUsuario->fetch_assoc();

$usuario_id =
    $usuario['id'];

// TOTAL

$total =
    $producto['precio'] * $cantidad;

// VENTA

$tipo = 'PRODUCTO';

$descripcion =
    'Venta de ' .
    $producto['nombre'] .
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

if($stmtVenta->execute()){

    echo json_encode([

        "success" => true,

        "message" =>
        "Venta registrada correctamente"

    ]);

}else{

    echo json_encode([

        "success" => false,

        "message" =>
        "Error al registrar venta"

    ]);

}