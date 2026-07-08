<?php

header('Content-Type: application/json');

require_once '../php_action/conn_db.php';

/*=========================================
VALIDAR DATOS
=========================================*/

if(empty($_POST['persona_id']) || empty($_POST['folio_cliente'])){

    echo json_encode([
        "success"=>false,
        "message"=>"Información incompleta."
    ]);

    exit();
}

if(!isset($_FILES['archivo'])){

    echo json_encode([
        "success"=>false,
        "message"=>"Debe seleccionar un comprobante."
    ]);

    exit();
}

$persona_id=intval($_POST['persona_id']);
$folio=trim($_POST['folio_cliente']);
$concepto=trim($_POST['concepto'] ?? '');
$archivo=$_FILES['archivo'];

//VALIDAR PERSONA

$sql="
    SELECT id
    FROM personas
    WHERE id=?
    AND folio=?
    LIMIT 1
";

$stmt=$connect->prepare($sql);
$stmt->bind_param(
    "is",
    $persona_id,
    $folio
);

$stmt->execute();

if($stmt->get_result()->num_rows==0){

    echo json_encode([
        "success"=>false,
        "message"=>"Cliente no encontrado."
    ]);

    exit();
}

//VALIDAR TAMAÑO

$maxSize = 10*1024*1024;

if($archivo['size'] > $maxSize){

    echo json_encode([
        "success"=>false,
        "message"=>"El archivo supera los 10MB."
    ]);

    exit();
}

//VALIDAR EXTENSION

$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

$permitidas = [
    'jpg',
    'jpeg',
    'png',
    'pdf'
];

if(!in_array($extension, $permitidas)){

    echo json_encode([
        "success"=>false,
        "message"=>"Formato no permitido."
    ]);

    exit();
}

//VALIDAR MIME

$finfo = new finfo(FILEINFO_MIME_TYPE);

$mime = $finfo->file($archivo['tmp_name']);

$mimesPermitidos = [

    'image/jpeg',

    'image/png',

    'application/pdf'

];

if(!in_array($mime, $mimesPermitidos)){

    echo json_encode([
        "success"=>false,
        "message"=>"Archivo inválido."
    ]);

    exit();
}

//CREAR CARPETAS

$año = date('Y');
$mes = date('m');

$rutaBase = "../uploads/comprobantes/";
$rutaCliente = $rutaBase.$folio."/";
$rutaFinal = $rutaCliente.$año."/".$mes."/";

if(!is_dir($rutaFinal)){

    mkdir($rutaFinal, 0775, true);
}

//GENERAR NOMBRE

$nombreArchivo = "CMP_" . bin2hex(random_bytes(16)) . "." . $extension;

//MOVER ARCHIVO

$rutaCompleta = $rutaFinal . $nombreArchivo;

if(!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)){

    echo json_encode([
        "success"=>false,
        "message"=>"No fue posible guardar el archivo."
    ]);

    exit();
}

//RUTA RELATIVA

$rutaBD = "comprobantes/" . $folio . "/" . $año . "/" . $mes . "/" . $nombreArchivo;

//VALIDAR SI HAY UNO PENDIENTE

$sql = "
    SELECT id
    FROM comprobantes_pago
    WHERE persona_id = ?
    AND status = 'PENDIENTE'
    LIMIT 1
";

$stmt = $connect->prepare($sql);
$stmt->bind_param(
    "i",
    $persona_id
);

$stmt->execute();

if($stmt->get_result()->num_rows > 0){

    echo json_encode([

        "success" => false,
        "message" => "Ya tienes un comprobante pendiente de revisión."

    ]);

    exit();
}

//INSERTAR EN BD

$sql="

    INSERT INTO comprobantes_pago
    (
        persona_id,
        folio_cliente,
        archivo,
        concepto,
        status
    )

    VALUES

    (
        ?,?,?,?, 'PENDIENTE'
    )

";

$stmt = $connect->prepare($sql);
$stmt->bind_param(

"isss",

$persona_id,
$folio,
$rutaBD,
$concepto

);

if($stmt->execute()){

    echo json_encode([

        "success"=>true,
        "message"=>"Comprobante enviado correctamente."

    ]);

}else{

    echo json_encode([

        "success"=>false,
        "message"=>"No fue posible registrar el comprobante."

    ]);

}

$stmt->close();
$connect->close();