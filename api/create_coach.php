<?php

header('Content-Type : application/json');

require_once '../php_action/conn_db.php';

//VALIDACION DE DATOS

if(empty($_POST['nombre']) || empty($_POST['edad']) || empty($_POST['especialidad']) || empty($_POST['descripcion'])){

    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios'
    ]);

    exit();
}

//VALIDAR FOTO

if(!isset($_FILES['foto']) || $_FILES['foto']['error'] !== 0){
    echo json_encode([
        'success' => false,
        'message' => 'Debes de seleccionar una fotografía'
    ]);

    exit();
}

$nombre = trim($_POST['nombre']);
$edad = trim($_POST['edad']);
$especialidad = trim($_POST['especialidad']);
$descripcion = trim($_POST['descripcion']);
$foto = $_FILES['foto'];

//VALIDAR IMAGEN
$permitidos = [
    'image/jpeg',
    'image/jpg',
    'image/png',
    'image/webp'
];

if(!in_array($foto['type'], $permitidos)){
    echo json_encode([
        'success' => false,
        'message' => 'Formato de imagen no permitido'
    ]);

    exit();
}

//VALIDAR TAMAÑO
$maxSize = 5 * 1024 * 1024;

if($foto['size'] > $maxSize){
    echo json_encode([
        'success' => false,
        'message' => 'La imagen supera los 5MB.'
    ]);

    exit();
}

//CREAR NOMBRE UNICO
$extension = pathinfo($foto['name'], PATHINFO_EXTENSION);

$nombreFoto = uniqid('coach_') . '.' . $extension;

//RUTA

$rutaFisica = dirname(__DIR__) . '/uploads/coaches/' . $nombreFoto;

$rutaBD = '/uploads/coaches/' . $nombreFoto;

//GUARDAR FOTO

if(!move_uploaded_file($foto['tmp_name'], $rutaFisica)){
    echo json_encode([
        'success' => false,
        'message' => 'No fue posible guardar la fotografía.'
    ]);

    exit();
}

//INSERTAR EN MYSQL

$sql = "
    INSERT INTO coaches(
        nombre,
        edad,
        especialidad,
        descripcion,
        foto
    ) VALUES (?,?,?,?,?)
";

$stmt = $connect->prepare($sql);

$stmt->bind_param("sisss",
    $nombre,
    $edad,
    $especialidad,
    $descripcion,
    $rutaBD    
);

if($stmt->execute()){

    echo json_encode([
        'success' => true,
        'message' => 'Coach registrado correctamente.'
    ]);
}else{

    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar coach.'
    ]);
}

$stmt->close();
$connect->close();

?>