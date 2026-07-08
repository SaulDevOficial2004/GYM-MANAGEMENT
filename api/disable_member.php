<?php

session_start();

header('Content-Type: application/json');

require_once "../php_action/conn_db.php";

//=======================================
// VALIDAR SESIÓN
//=======================================

if(!isset($_SESSION['telefono'])){

    echo json_encode([
        "success" => false,
        "message" => "Sesión expirada."
    ]);

    exit();

}

//=======================================
// VALIDAR ID
//=======================================

if(empty($_POST['id'])){

    echo json_encode([
        "success" => false,
        "message" => "Cliente inválido."
    ]);

    exit();

}

$id = intval($_POST['id']);

//=======================================
// INHABILITAR
//=======================================

$sql = "

    UPDATE personas
    SET estatus = 2
    WHERE id = ?

";

$stmt = $connect->prepare($sql);

$stmt->bind_param("i", $id);

if($stmt->execute()){

    echo json_encode([

        "success" => true,
        "message" => "Cliente inhabilitado correctamente."

    ]);

}else{

    echo json_encode([

        "success" => false,
        "message" => "No fue posible inhabilitar al cliente."

    ]);

}

$stmt->close();

$connect->close();

?>