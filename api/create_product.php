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

if($stmt->execute()){

    echo json_encode([
        "success" => true,
        "message" => "Producto registrado correctamente"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Error al registrar producto"
    ]);
}