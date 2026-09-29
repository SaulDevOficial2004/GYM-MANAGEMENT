<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

require_once __DIR__.'/../php_action/conn_db.php';

function respondLogin(int $httpCode,string $status,string $message,array $extra=[]):void
{
    require_once __DIR__ . '/../includes/response.php';

    jsonResponse(array_merge([
        'status'=>$status,
        'message'=>$message
    ],$extra),$httpCode,JSON_UNESCAPED_UNICODE);
}

if($_SERVER['REQUEST_METHOD']!=='POST'){
    respondLogin(
        405,
        'error',
        'Método de solicitud no permitido.'
    );
}

$contentType=$_SERVER['CONTENT_TYPE']??'';

if(stripos($contentType,'application/json')===false){
    respondLogin(
        415,
        'error',
        'El formato de la solicitud no es válido.'
    );
}

$rawBody=file_get_contents('php://input');
$data=json_decode($rawBody,true);

if(!is_array($data)){
    respondLogin(
        400,
        'error',
        'La información enviada no es válida.'
    );
}

$telefono=trim((string)($data['telefono']??''));
$password=(string)($data['password']??'');

if($telefono===''||$password===''){
    respondLogin(
        422,
        'error',
        'Teléfono y contraseña son obligatorios.'
    );
}

if(mb_strlen($telefono)>20){
    respondLogin(
        422,
        'error',
        'El número de teléfono no es válido.'
    );
}

if(mb_strlen($password)>255){
    respondLogin(
        422,
        'error',
        'La contraseña no es válida.'
    );
}

require_once __DIR__.'/../includes/rate_limit.php';

/*
|--------------------------------------------------------------------------
| PROTECCIÓN CONTRA INTENTOS REPETIDOS (persistente en BD)
|--------------------------------------------------------------------------
*/

$ipIdentificador = 'ip:' . getClientIp();

if (rateLimit($telefono, 'login_telefono', 5, 900)) {
    respondLogin(
        429,
        'error',
        'Demasiados intentos. Intenta nuevamente en 15 minutos.'
    );
}

if (rateLimit($ipIdentificador, 'login_ip', 20, 900)) {
    respondLogin(
        429,
        'error',
        'Demasiados intentos desde esta dirección.'
    );
}

if (random_int(1, 100) === 1) {
    pruneLoginAttempts();
}

$sql="
    SELECT
        u.id,
        u.nombre,
        u.telefono,
        u.password,
        u.rol_id,
        r.nombre AS rol_nombre
    FROM usuarios u
    INNER JOIN roles r ON r.id=u.rol_id
    WHERE u.telefono=?
      AND u.activo=1
    LIMIT 1
";

$stmt=$connect->prepare($sql);

if(!$stmt){
    error_log(
        'Error preparando login: '.$connect->error
    );

    respondLogin(
        500,
        'error',
        'No fue posible procesar el inicio de sesión.'
    );
}

$stmt->bind_param('s',$telefono);

if(!$stmt->execute()){
    error_log(
        'Error ejecutando login: '.$stmt->error
    );

    $stmt->close();
    $connect->close();

    respondLogin(
        500,
        'error',
        'No fue posible procesar el inicio de sesión.'
    );
}

$result=$stmt->get_result();
$user=$result->fetch_assoc();

$validCredentials=
    is_array($user)
    &&password_verify(
        $password,
        (string)$user['password']
    );

if(!$validCredentials){
    recordLoginAttempt($telefono, false);
    recordLoginAttempt($ipIdentificador, false);
    $stmt->close();
    $connect->close();

    usleep(350000);

    respondLogin(
        401,
        'error',
        'El teléfono o la contraseña son incorrectos.'
    );
}

session_regenerate_id(true);

$_SESSION['user_id']=(int)$user['id'];
$_SESSION['telefono']=$user['telefono'];
$_SESSION['nombre']=$user['nombre'];
$_SESSION['rol_id']=(int)$user['rol_id'];
$_SESSION['rol_nombre']=$user['rol_nombre'];

recordLoginAttempt($telefono, true);
clearFailedLoginAttempts($telefono);

$_SESSION['last_activity']=time();
$_SESSION['login_time']=time();
$_SESSION['id_rotado']=time();

$stmt->close();
$connect->close();

respondLogin(
    200,
    'success',
    'Bienvenido '.$user['nombre'],
    [
        'redirect'=>'pagina.php',
        'user'=>[
            'nombre'=>$user['nombre'],
            'rol'=>$user['rol_nombre']
        ]
    ]
);