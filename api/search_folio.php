<?php

header('Content-Type: application/json');

require_once '../php_action/conn_db.php';

$folio = trim($_POST['folio'] ?? '');

if(empty($folio)){
    echo json_encode([

        'success' => false,
        'message' => 'Ingrese un folio.'
    ]);

    exit();
}

$sql = "
    SELECT
        id,
        nombre,
        folio
    FROM personas
    WHERE folio = ?
    LIMIT 1
";

$stmt = $connect->prepare($sql);

$stmt->bind_param(
    "s",
    $folio
);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows > 0){

    echo json_encode([

        'success' => true,

        'redirect' =>
        'transferencias_cliente.php?folio=' .
        urlencode($folio)

    ]);

}else{

    echo json_encode([

        'success' => false,

        'message' =>
        'Folio no encontrado.'

    ]);
}

$stmt->close();
$connect->close();