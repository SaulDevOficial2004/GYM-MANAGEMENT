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

require_once "../php_action/conn_db.php";

//VALIDAR SESION

if(!isset($_SESSION['telefono'])){

    jsonResponse([
        "success"=>false,
        "message"=>"Sesión expirada."
    ]);
}

//VALIDAR DATOS

if(empty($_POST['id']) || empty(trim($_POST['motivo']))){

    jsonResponse([
        "success"=>false,
        "message"=>"Debes escribir un motivo."
    ]);
}

$id = intval($_POST['id']);
$motivo = trim($_POST['motivo']);

//OBTENER USUARIO

$telefono = $_SESSION['telefono'];

$sqlUsuario = "
    SELECT id
    FROM usuarios
    WHERE telefono = ?
    LIMIT 1
";

$stmtUsuario = $connect->prepare($sqlUsuario);

$stmtUsuario->bind_param("s",
    $telefono
);

$stmtUsuario->execute();

$resultUsuario = $stmtUsuario->get_result();

if($resultUsuario->num_rows == 0){

    throw new Exception("Usuario no encontrado.");

}

$usuario = $resultUsuario->fetch_assoc();
$usuario_id = $usuario['id'];

//TRANSACCION

$connect->begin_transaction();

try{

    $sql = "

        UPDATE comprobantes_pago SET

            status='RECHAZADO',
            motivo_rechazo=?,
            revisado_por=?,
            fecha_revision=NOW()

        WHERE id=?

    ";

    $stmt = $connect->prepare($sql);

    $stmt->bind_param("sii",

        $motivo,
        $usuario_id,
        $id

    );

    if(!$stmt->execute()){

        throw new Exception("No fue posible rechazar el comprobante.");

    }

    $connect->commit();

    jsonResponse([

        "success"=>true,
        "message"=>"Comprobante rechazado."

    ]);

}catch(Exception $e){

    $connect->rollback();

    jsonResponse([

        "success"=>false,
        "message"=>$e->getMessage()

    ]);

}

?>