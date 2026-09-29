<?php

require_once __DIR__ . '/../includes/api_auth.php';

requireApiRoles([
    'Administrador',
    'Dueño',
    'Recepcionista'
]);

require_once '../php_action/conn_db.php';

$search = $_GET['search'] ?? '';

$sql = "
    SELECT *
    FROM visitantes
    WHERE nombre LIKE ?
    ORDER BY nombre ASC
    LIMIT 10
";

$term = "%".$search."%";

$stmt = $connect->prepare($sql);
$stmt->bind_param("s",$term);
$stmt->execute();

$result = $stmt->get_result();

$data = [];

while($row = $result->fetch_assoc()){

    $data[] = $row;

}

jsonResponse($data);

$stmt->close();
$connect->close();