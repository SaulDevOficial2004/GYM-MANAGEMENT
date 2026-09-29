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
$estado = (int) ($_POST['estado'] ?? -1);

if ($id <= 0 || !in_array($estado, [0, 1], true)) {

    jsonResponse([
        'success' => false,
        'message' => 'Los datos enviados no son válidos.'
    ]);
}

/* VALIDAR QUE SEA RECEPCIONISTA */

$sqlReceptionist = "

    SELECT

        u.id,
        u.nombre,
        u.activo

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

$receptionist = $resultReceptionist->fetch_assoc();

$stmtReceptionist->close();

if ((int) $receptionist['activo'] === $estado) {

    jsonResponse([
        'success' => false,
        'message' => $estado === 1 ? 'El recepcionista ya está activo.' : 'El recepcionista ya está inactivo.'
    ]);
}

/* ACTUALIZAR ESTADO */

$sqlUpdate = "

    UPDATE usuarios
    SET activo = ?
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

    'ii',

    $estado,
    $id

);

$connect->begin_transaction();

try{

if (!$stmtUpdate->execute()) {

    $stmtUpdate->close();

    $connect->rollback();

    jsonResponse([
        'success' => false,
        'message' => 'No fue posible actualizar el estado.'
    ]);
}

require_once __DIR__ . '/../includes/audit.php';

$usuario_id = apiCurrentUserId();

$accionBitacora = $estado === 1
    ? 'ACTIVAR'
    : 'DESACTIVAR';

$descripcionBitacora = $estado === 1
    ? "Activó al recepcionista {$receptionist['nombre']}"
    : "Desactivó al recepcionista {$receptionist['nombre']}";

if (!registerActivity(

    $connect,
    $usuario_id,
    $accionBitacora,
    'RECEPCIONISTAS',
    $id,
    $descripcionBitacora

)) {
    $connect->rollback();
    apiError('No se pudo completar la operación.', 500);
}

$connect->commit();

$accion = $estado === 1 ? 'activado' : 'desactivado';

jsonResponse([
    'success' => true,
    'message' => "Recepcionista {$accion} correctamente."
]);

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en toggle_receptionist: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

$stmtUpdate->close();
$connect->close();