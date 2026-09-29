<?php

require_once __DIR__ . '/../includes/api_auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    apiError('Método no permitido', 405);
}

requireApiRoles([
    'Administrador',
    'Dueño'
]);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../php_action/conn_db.php';

$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password'] ?? '';

if ($nombre === '' || $telefono === '' || $password === '') {

    jsonResponse([
        'success' => false,
        'message' => 'Todos los campos son obligatorios.'
    ]);
}

if (mb_strlen($nombre) < 3) {

    jsonResponse([
        'success' => false,
        'message' => 'El nombre debe contener al menos 3 caracteres.'
    ]);
}

if (!preg_match('/^[0-9]{10}$/', $telefono)) {

    jsonResponse([
        'success' => false,
        'message' => 'El teléfono debe contener exactamente 10 números.'
    ]);
}

if (strlen($password) < 6) {

    jsonResponse([
        'success' => false,
        'message' => 'La contraseña debe contener al menos 6 caracteres.'
    ]);
}

/* COMPROBAR TELÉFONO */

$sqlTelefono = "

    SELECT id
    FROM usuarios
    WHERE telefono = ?
    LIMIT 1

";

$stmtTelefono = $connect->prepare($sqlTelefono);

if (!$stmtTelefono) {

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible validar el teléfono.'
    ]);
}

$stmtTelefono->bind_param(
    's',
    $telefono
);

$stmtTelefono->execute();
$resultTelefono = $stmtTelefono->get_result();

if ($resultTelefono->num_rows > 0) {

    $stmtTelefono->close();

    jsonResponse([
        'success' => false,
        'message' => 'El teléfono ya pertenece a otro usuario.'
    ]);
}

$stmtTelefono->close();

/* OBTENER ROL RECEPCIONISTA */

$sqlRol = "

    SELECT id
    FROM roles
    WHERE nombre = 'Recepcionista'
    LIMIT 1

";

$resultRol = $connect->query($sqlRol);

if (!$resultRol || $resultRol->num_rows !== 1) {

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible encontrar el rol Recepcionista.'
    ]);
}

$rowRol = $resultRol->fetch_assoc();

$rol_id = (int) $rowRol['id'];

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

/* REGISTRAR RECEPCIONISTA */

$sqlCreate = "

    INSERT INTO usuarios (

        nombre,
        telefono,
        password,
        rol_id,
        activo

    ) VALUES (?, ?, ?, ?, 1)

";

$stmtCreate = $connect->prepare($sqlCreate);

if (!$stmtCreate) {

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible preparar el registro.'
    ]);
}

$stmtCreate->bind_param(

    'sssi',

    $nombre,
    $telefono,
    $passwordHash,
    $rol_id

);

$connect->begin_transaction();

try{

if (!$stmtCreate->execute()) {

    $stmtCreate->close();

    $connect->rollback();

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible registrar al recepcionista.'
    ]);
}

$receptionist_id = $connect->insert_id;

require_once __DIR__ . '/../includes/audit.php';

$usuario_id = apiCurrentUserId();

if (!registerActivity(

    $connect,
    $usuario_id,
    'CREAR',
    'RECEPCIONISTAS',
    $receptionist_id,
    "Registró al recepcionista {$nombre}"

)) {
    $connect->rollback();
    apiError('No se pudo completar la operación.', 500);
}

$connect->commit();

jsonResponse([
    'success' => true,
    'message' => 'Recepcionista registrado correctamente.'
]);

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en create_receptionist: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

$stmtCreate->close();
$connect->close();