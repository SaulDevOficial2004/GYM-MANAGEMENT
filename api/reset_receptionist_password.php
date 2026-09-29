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

$id = (int) ($_POST['id'] ?? 0);
$password = $_POST['password'] ?? '';

if ($id <= 0 || $password === '') {

    jsonResponse([
        'success' => false,
        'message' => 'Los datos enviados no son válidos.'
    ]);

}

if (strlen($password) < 6) {

    jsonResponse([
        'success' => false,
        'message' => 'La contraseña debe contener al menos 6 caracteres.'
    ]);

}

/* VALIDAR RECEPCIONISTA */

$sqlReceptionist = "

    SELECT

        u.id,
        u.nombre

    FROM usuarios u
    INNER JOIN roles r
    ON r.id = u.rol_id
    WHERE u.id = ?
    AND r.nombre = 'Recepcionista'
    LIMIT 1

";

$stmtReceptionist = $connect->prepare($sqlReceptionist);

if (!$stmtReceptionist) {

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible validar al recepcionista.'
    ]);

}

$stmtReceptionist->bind_param(
    'i',
    $id
);

$stmtReceptionist->execute();

$resultReceptionist = $stmtReceptionist->get_result();
$receptionist = $resultReceptionist->fetch_assoc();

if ($resultReceptionist->num_rows !== 1) {

    $stmtReceptionist->close();

    jsonResponse([
        'success' => false,
        'message' => 'El recepcionista no existe.'
    ]);

}

$stmtReceptionist->close();

/* ACTUALIZAR CONTRASEÑA */

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$sqlUpdate = "

    UPDATE usuarios
    SET password = ?
    WHERE id = ?

";

$stmtUpdate = $connect->prepare($sqlUpdate);

if (!$stmtUpdate) {

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible preparar la actualización.'
    ]);

}

$stmtUpdate->bind_param(

    'si',

    $passwordHash,
    $id

);

$connect->begin_transaction();

try{

if (!$stmtUpdate->execute()) {

    $stmtUpdate->close();

    $connect->rollback();

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible restablecer la contraseña.'
    ]);
}

require_once __DIR__ . '/../includes/audit.php';

$usuario_id = apiCurrentUserId();

if (!registerActivity(

    $connect,
    $usuario_id,
    'RESTABLECER_CONTRASEÑA',
    'RECEPCIONISTAS',
    $id,
    "Restableció la contraseña del recepcionista {$receptionist['nombre']}"

)) {
    $connect->rollback();
    apiError('No se pudo completar la operación.', 500);
}

$connect->commit();

jsonResponse([
    'success' => true,
    'message' => 'Contraseña restablecida correctamente.'
]);

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en reset_receptionist_password: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

$stmtUpdate->close();
$connect->close();