<?php

require_once '../php_action/conn_db.php';

$id = $_POST['id'];
$estado = $_POST['estado'];

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

$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => "Estado actualizado"
]);