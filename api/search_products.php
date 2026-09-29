<?php

require_once __DIR__ . '/../includes/api_auth.php';

requireApiRoles([
    'Administrador',
    'Dueño',
    'Recepcionista'
]);

require_once '../php_action/conn_db.php';

$search = $_GET['search'];

$sql = "
    SELECT
        id,
        nombre,
        precio,
        stock
    FROM productos
    WHERE activo = 1
    AND nombre LIKE ?
    ORDER BY nombre ASC
";

$stmt = $connect->prepare($sql);

$term = "%{$search}%";

$stmt->bind_param(
    "s",
    $term
);

$stmt->execute();

$result = $stmt->get_result();

$data = [];

while($row = $result->fetch_assoc()){

    $data[] = $row;
}

jsonResponse($data);