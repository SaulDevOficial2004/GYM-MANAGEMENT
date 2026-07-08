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

$telefono = $_SESSION['telefono'];

$sqlUsuario = "
    SELECT id
    FROM usuarios
    WHERE telefono = ?
";

$stmtUsuario = $connect->prepare($sqlUsuario);

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

$tipo = 'TOALLA';

$descripcion =
    'Renta de toalla';

$total = 25.00;

$sqlVenta = "
    INSERT INTO ventas
    (
        usuario_id,
        tipo,
        descripcion,
        total
    )
    VALUES
    (
        ?, ?, ?, ?
    )
";

$stmtVenta = $connect->prepare(
    $sqlVenta
);

$stmtVenta->bind_param(

    "issd",

    $usuario_id,
    $tipo,
    $descripcion,
    $total

);

if($stmtVenta->execute()){

    echo json_encode([

        "success" => true,

        "message" =>
        "Renta registrada correctamente"

    ]);

}else{

    echo json_encode([

        "success" => false,

        "message" =>
        "Error al registrar renta"

    ]);

}