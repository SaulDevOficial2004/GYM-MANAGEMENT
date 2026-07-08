<?php

session_start();

header('Content-Type: application/json');

require_once '../php_action/conn_db.php';

$id = $_POST['id'];

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
    $_POST['nombre'],
    $_POST['descripcion'],
    $_POST['precio'],
    $_POST['stock'],
    $id
);

if($stmt->execute()){

    echo json_encode([
        "success" => true,
        "message" => "Producto actualizado"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Error al actualizar"
    ]);
}