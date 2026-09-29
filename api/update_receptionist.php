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
$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password'] ?? '';

if ($id <= 0 || $nombre === '' || $telefono === '') {

    jsonResponse([
        'success' => false,
        'message' => 'Los datos del recepcionista no son válidos.'
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

if ($password !== '' && strlen($password) < 6) {

    jsonResponse([
        'success' => false,
        'message' => 'La nueva contraseña debe contener al menos 6 caracteres.'
    ]);
}

/* VALIDAR RECEPCIONISTA */

$sqlReceptionist = "

    SELECT

        u.id,
        u.rol_id

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

if ($resultReceptionist->num_rows !== 1) {

    $stmtReceptionist->close();

    jsonResponse([
        'success' => false,
        'message' => 'El recepcionista no existe.'
    ]);
}

$stmtReceptionist->close();

/* VALIDAR TELÉFONO DUPLICADO */

$sqlPhone = "

    SELECT id
    FROM usuarios
    WHERE telefono = ?
    AND id <> ?
    LIMIT 1

";

$stmtPhone = $connect->prepare($sqlPhone);

if (!$stmtPhone) {

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible validar el teléfono.'
    ]);
}

$stmtPhone->bind_param(
    'si',
    $telefono,
    $id
);

$stmtPhone->execute();

$resultPhone = $stmtPhone->get_result();

if ($resultPhone->num_rows > 0) {

    $stmtPhone->close();

    jsonResponse([
        'success' => false,
        'message' => 'El teléfono ya pertenece a otro usuario.'
    ]);
}

$stmtPhone->close();

/* ACTUALIZAR RECEPCIONISTA */

if ($password !== '') {

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sqlUpdate = "

        UPDATE usuarios
        SET

            nombre = ?,
            telefono = ?,
            password = ?

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

        'sssi',

        $nombre,
        $telefono,
        $passwordHash,
        $id

    );

} else {

    $sqlUpdate = "

        UPDATE usuarios
        SET

            nombre = ?,
            telefono = ?

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

        'ssi',

        $nombre,
        $telefono,
        $id

    );

}

$connect->begin_transaction();

try{

if (!$stmtUpdate->execute()) {

    $stmtUpdate->close();

    $connect->rollback();

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible actualizar al recepcionista.'
    ]);
}

require_once __DIR__ . '/../includes/audit.php';

$usuario_id = apiCurrentUserId();

if (!registerActivity(

    $connect,
    $usuario_id,
    'EDITAR',
    'RECEPCIONISTAS',
    $id,
    "Actualizó los datos del recepcionista {$nombre}"

)) {
    $connect->rollback();
    apiError('No se pudo completar la operación.', 500);
}

$connect->commit();

jsonResponse([
    'success' => true,
    'message' => 'Recepcionista actualizado correctamente.'
]);

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en update_receptionist: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

$stmtUpdate->close();
$connect->close();