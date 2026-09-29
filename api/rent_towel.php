<?php

require_once __DIR__ . '/../includes/api_auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    apiError('Método no permitido', 405);
}

requireApiRoles([
    'Administrador',
    'Dueño',
    'Recepcionista'
]);

header('Content-Type: application/json');

require_once '../php_action/conn_db.php';

if(!isset($_SESSION['telefono'])){

    jsonResponse([
        "success" => false,
        "message" => "Sesión expirada"
    ]);
}

$telefono = $_SESSION['telefono'];

$sqlUsuario = "
    SELECT id
    FROM usuarios
    WHERE telefono = ?
";

$stmtUsuario = $connect->prepare($sqlUsuario);

$stmtUsuario->bind_param(
    "s",
    $telefono
);

$stmtUsuario->execute();

$resultUsuario =
    $stmtUsuario->get_result();

$usuario =
    $resultUsuario->fetch_assoc();

$usuario_id =
    $usuario['id'];

$tipo = 'TOALLA';

$descripcion =
    'Renta de toalla';

$total = 25.00;

require_once __DIR__ . '/../includes/audit.php';

// TRANSACCION

$connect->begin_transaction();

try{

    $sqlVenta = "
        INSERT INTO ventas
        (
            usuario_id,
            tipo,
            descripcion,
            total
        )
        VALUES
        (
            ?, ?, ?, ?
        )
    ";

    $stmtVenta = $connect->prepare(
        $sqlVenta
    );

    $stmtVenta->bind_param(

        "issd",

        $usuario_id,
        $tipo,
        $descripcion,
        $total

    );

    $stmtVenta->execute();

    $venta_id = $connect->insert_id;

    // BITACORA

    if (!registerActivity(

        $connect,
        $usuario_id,
        'CREAR',
        'VENTAS',
        $venta_id,
        $descripcion

    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    $connect->commit();

    jsonResponse([

        "success" => true,

        "message" =>
        "Renta registrada correctamente"

    ]);

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en rent_towel: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}