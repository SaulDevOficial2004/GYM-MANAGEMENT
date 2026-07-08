<?php

require_once '../php_action/conn_db.php';

$id = $_POST['id'];

$sql = "
    DELETE FROM productos
    WHERE id = ?
";

$stmt = $connect->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => "Producto eliminado"
]);