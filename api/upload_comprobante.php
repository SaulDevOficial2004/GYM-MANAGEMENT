<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/signed_link.php';
require_once __DIR__ . '/../includes/rate_limit.php';

validarEnlaceCliente(true);

function responderComprobante(bool $exito, string $mensaje): void
{
    echo json_encode([
        'success' => $exito,
        'message' => $mensaje
    ]);

    exit();
}

function verificarTurnstile(string $token): bool
{
    if (TURNSTILE_SECRET === '') {
        return true;
    }

    if ($token === '') {
        return false;
    }

    $payload = http_build_query([
        'secret' => TURNSTILE_SECRET,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ]);

    $contexto = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $payload,
            'timeout' => 10
        ]
    ]);

    $crudo = @file_get_contents(
        'https://challenges.cloudflare.com/turnstile/v0/siteverify',
        false,
        $contexto
    );

    if ($crudo === false) {
        return false;
    }

    $respuesta = json_decode($crudo, true);

    return is_array($respuesta) && ($respuesta['success'] ?? false) === true;
}

$ipCliente = getClientIp();

if (rateLimit('pub_upload:' . $ipCliente, 'upload', 5, 3600)) {
    http_response_code(429);
    responderComprobante(false, 'Demasiados comprobantes desde esta dirección. Intenta más tarde.');
}

if (random_int(1, 100) === 1) {
    pruneLoginAttempts();
}

/*=========================================
VALIDAR DATOS
=========================================*/

if(empty($_POST['persona_id']) || empty($_POST['folio_cliente'])){

    responderComprobante(false, "Información incompleta.");
}

if(!isset($_FILES['archivo'])){

    responderComprobante(false, "Debe seleccionar un comprobante.");
}

$persona_id=intval($_POST['persona_id']);
$folio=trim($_POST['folio_cliente']);
$concepto=trim($_POST['concepto'] ?? '');
$archivo=$_FILES['archivo'];

if(preg_match('/^CLI-[A-F0-9]{6}$/', $folio) !== 1){
    responderComprobante(false, 'Folio no válido.');
}

if (rateLimit('pub_upload_persona:' . $persona_id, 'upload', 3, 86400)) {
    http_response_code(429);
    responderComprobante(false, 'Límite diario de comprobantes alcanzado.');
}

recordLoginAttempt('pub_upload:' . $ipCliente, false);
recordLoginAttempt('pub_upload_persona:' . $persona_id, false);

if(!verificarTurnstile(trim($_POST['turnstile_token'] ?? ''))){
    responderComprobante(false, 'Verificación de seguridad fallida.');
}

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

    responderComprobante(false, "Cliente no encontrado.");
}

//VALIDAR TAMAÑO

$maxSize = 5*1024*1024;

if($archivo['size'] > $maxSize){

    responderComprobante(false, "El archivo supera los 5MB.");
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

    responderComprobante(false, "Formato no permitido.");
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

    responderComprobante(false, "Archivo inválido.");
}

//CREAR CARPETAS

$año = date('Y');
$mes = date('m');

$rutaBase = STORAGE_PATH . "/comprobantes/";
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

    responderComprobante(false, "No fue posible guardar el archivo.");
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

    responderComprobante(false, "Ya tienes un comprobante pendiente de revisión.");
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

    jsonResponse([

        "success"=>true,
        "message"=>"Comprobante enviado correctamente."

    ]);

}else{

    jsonResponse([

        "success"=>false,
        "message"=>"No fue posible registrar el comprobante."

    ]);

}

$stmt->close();
$connect->close();