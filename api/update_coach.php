<?php

header('Content-Type: application/json');

require_once '../php_action/conn_db.php';

//VALIDACIONES

if(empty($_POST['id']) || empty($_POST['nombre']) || empty($_POST['edad']) || empty($_POST['especialidad']) || empty($_POST['descripcion'])){

    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios.'
    ]);

    exit();
}

$id = intval($_POST['id']);
$nombre = trim($_POST['nombre']);
$edad = intval($_POST['edad']);
$especialidad = trim($_POST['especialidad']);
$descripcion = trim($_POST['descripcion']);

//COACH ACTUAL

$sqlCoach = "
    SELECT foto
    FROM coaches
    WHERE id = ?
";

$stmtCoach = $connect->prepare($sqlCoach);
$stmtCoach->bind_param("i", $id);
$stmtCoach->execute();

$resultCoach = $stmtCoach->get_result();
$coach = $resultCoach->fetch_assoc();

if(!$coach){

    echo json_encode([
        'success' => false,
        'message' => 'Coach no encontrado.'
    ]);

    exit();
}

$fotoActual = $coach['foto'];
$nuevaRutaFoto = $fotoActual;

//¿SUBIO UNA NUEVA FOTO?

if(isset($_FILES['foto']) && $_FILES['foto']['error'] === 0){

    $foto = $_FILES['foto'];

    $permitidos = [

        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp'
    ];

    if(!in_array($foto['type'], $permitidos)){
        
        echo json_encode([
            'success' => false,
            'message' => 'Formato de imagen inválido.'
        ]);

        exit();
    }

    $extension = pathinfo($foto['name'], PATHINFO_EXTENSION);

    $nombreFoto = uniqid('coach_') . '.' . $extension;

    $rutaFisica = dirname(__DIR__) . '/uploads/coaches/' . $nombreFoto;

    $nuevaRutaFoto = '/uploads/coaches/' . $nombreFoto;

    if(move_uploaded_file($foto['tmp_name'], $rutaFisica)){
        //ELIMINAR FOTO ANTERIOR
        $fotoAnterior = dirname(__DIR__) . '/' . $fotoActual;

        if(file_exists($fotoAnterior)){
            
            unlink($fotoAnterior);
        }
    }
}

//UPDATE

$sql = "
    UPDATE coaches
    SET
    nombre = ?,
    edad = ?,
    especialidad = ?,
    descripcion = ?,
    foto = ?
    WHERE id = ?
";

$stmt = $connect->prepare($sql);
$stmt->bind_param("sisssi",
    $nombre,
    $edad,
    $especialidad,
    $descripcion,
    $nuevaRutaFoto,
    $id
);

if($stmt->execute()){

    echo json_encode([
        'success' => true,
        'message' => 'Coach actualizado correctamente.'
    ]);
}else{

    echo json_encode([
        'success' => false,
        'message' => 'No fue posible actualizar.'
    ]);
}

$stmt->close();
$connect->close();
?>