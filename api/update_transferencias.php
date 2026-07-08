<?php

session_start();

header('Content-Type: application/json');

require_once "../php_action/conn_db.php";

//VALIDAR SESION

if(!isset($_SESSION['telefono'])){

    echo json_encode([
        "success"=>false,
        "message"=>"Sesión expirada."
    ]);

    exit();

}

//VALIDAR DATOS

if(empty($_POST['id']) || empty(trim($_POST['banco'])) || empty(trim($_POST['titular'])) || empty(trim($_POST['clabe']))){

    echo json_encode([
        "success"=>false,
        "message"=>"Todos los campos son obligatorios."
    ]);

    exit();

}

$id = intval($_POST['id']);
$banco = trim($_POST['banco']);
$titular = trim($_POST['titular']);
$clabe = trim($_POST['clabe']);

//ACTUALIZAR

$sql = "

    UPDATE configuracion_transferencias SET

        banco = ?,
        titular = ?,
        clabe = ?

    WHERE id = ?

";

$stmt = $connect->prepare($sql);
$stmt->bind_param("sssi",

    $banco,
    $titular,
    $clabe,
    $id

);

if($stmt->execute()){

    echo json_encode([

        "success"=>true,
        "message"=>"Datos bancarios actualizados correctamente."

    ]);

}else{

    echo json_encode([

        "success"=>false,
        "message"=>"No fue posible actualizar la configuración."

    ]);

}

$stmt->close();
$connect->close();

?>