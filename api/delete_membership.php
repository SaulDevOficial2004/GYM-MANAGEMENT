<?php

header('Content-Type: application/json');

require_once '../php_action/conn_db.php';

//VALIDACIONES

if(empty($_POST['id'])){

    echo json_encode([

        'success' => false,
        'message' => 'ID inválido.'
    ]);

    exit();

}

$id = intval($_POST['id']);

//ELIMINAR


$sql = "
    DELETE FROM membresias
    WHERE id = ?
";

$stmt = $connect->prepare($sql);
$stmt->bind_param(
    "i",
    $id
);

if($stmt->execute()){

    echo json_encode([

        'success' => true,
        'message' => 'Membresía eliminada correctamente.'
    ]);

}else{

    echo json_encode([

        'success' => false,
        'message' => 'No fue posible eliminar la membresía.'
    ]);

}

$stmt->close();
$connect->close();

?>