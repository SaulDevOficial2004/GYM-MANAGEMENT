<?php

header('Content-Type: application/json');

require_once '../php_action/conn_db.php';

//VALIDACION

if(empty($_POST['nombre']) || empty($_POST['precio'])){

    echo json_encode([
        'success' => false,
        'message' => 'Completa los campos obligatorios.'
    ]);

    exit();
}

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
            'message' => 'Debe ingresar el precio de la promoción.'
        ]);

        exit();
    }

    $precio_promocion = floatval($_POST['precio_promocion']);
}

//INSERT

$sql = "
    INSERT INTO membresias(
        nombre,
        descripcion,
        precio,
        promocion,
        precio_promocion,
        dias

    )VALUES(
        ?,?,?,?,?,?
    )
";

$stmt = $connect->prepare($sql);

$stmt->bind_param("ssdidi",
    $nombre,
    $descripcion,
    $precio,
    $promocion,
    $precio_promocion,
    $dias
);

//EJECUCION

if($stmt->execute()){

    echo json_encode([

        'success' => true,
        'message' => 'Membresía registrada correctamente.'
    ]);
}else{

    echo json_encode([
        'success' => false,
        'message' => 'No fue posible registrar la membresía.'
    ]);
}

$stmt->close();
$connect->close()
?>