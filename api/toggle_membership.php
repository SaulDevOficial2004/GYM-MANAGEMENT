<?php

header('Content-Type: application/json');

require_once '../php_action/conn_db.php';


//VALIDACIONES
if(empty($_POST['id']) || !isset($_POST['estado'])){

    echo json_encode([

        'success' => false,
        'message' => 'Datos incompletos.'
    ]);

    exit();

}

$id = intval($_POST['id']);
$estado = intval($_POST['estado']);

//VALIDAR ESTADO

if($estado !== 0 && $estado !== 1){

    echo json_encode([

        'success' => false,
        'message' => 'Estado inválido.'
    ]);

    exit();
}

//UPDATE

$sql = "
    UPDATE membresias
    SET activo = ?
    WHERE id = ?
";

$stmt = $connect->prepare($sql);
$stmt->bind_param(

    "ii",

    $estado,
    $id
);

if($stmt->execute()){

    $mensaje = $estado == 1
    ? 'Membresía activada correctamente.'
    : 'Membresía desactivada correctamente.';

    echo json_encode([

        'success' => true,
        'message' => $mensaje

    ]);

}else{

    echo json_encode([

        'success' => false,
        'message' => 'No fue posible actualizar el estado.'
    ]);

}

$stmt->close();
$connect->close();

?>