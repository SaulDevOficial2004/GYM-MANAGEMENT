<?php

header('Content-Type: application/json');

require_once '../php_action/conn_db.php';

//VALIDACIONES

if(empty($_POST['id']) || empty($_POST['nombre']) || empty($_POST['precio'])){

    echo json_encode([

        'success' => false,
        'message' => 'Complete los campos obligatorios.'

    ]);

    exit();

}

//VARIABLES

$id = intval($_POST['id']);
$nombre = trim($_POST['nombre']);
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = floatval($_POST['precio']);
$promocion = intval($_POST['promocion'] ?? 0);
$precio_promocion = null;
$dias = intval($_POST['dias']);

//VALIDAR PROMOCION 

if($promocion === 1){

    if(empty($_POST['precio_promocion'])){

        echo json_encode([

            'success' => false,
            'message' => 'Debe ingresar el precio de promoción.'

        ]);

        exit();

    }

    $precio_promocion = floatval($_POST['precio_promocion']);
}

//UPDATE

$sql = "
    UPDATE membresias
    SET
        nombre = ?,
        descripcion = ?,
        precio = ?,
        promocion = ?,
        precio_promocion = ?,
        dias = ?
    WHERE id = ?
";

$stmt = $connect->prepare($sql);

$stmt->bind_param(

    "ssdidii",

    $nombre,
    $descripcion,
    $precio,
    $promocion,
    $precio_promocion,
    $dias,
    $id
);

if($stmt->execute()){

    echo json_encode([

        'success' => true,
        'message' => 'Membresía actualizada correctamente.'

    ]);

}else{

    echo json_encode([

        'success' => false,
        'message' => 'No fue posible actualizar la membresía.'

    ]);

}

$stmt->close();
$connect->close();

?>