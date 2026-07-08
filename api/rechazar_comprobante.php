<?php

session_start();

header('Content-Type: application/json');

require_once "../php_action/conn_db.php";

//VALIDAR SESION

if(!isset($_SESSION['telefono'])){

    echo json_encode([
        "success"=>false,
        "message"=>"Sesión expirada."
    ]);

    exit();
}

//VALIDAR DATOS

if(empty($_POST['id']) || empty(trim($_POST['motivo']))){

    echo json_encode([
        "success"=>false,
        "message"=>"Debes escribir un motivo."
    ]);

    exit();
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

    echo json_encode([

        "success"=>true,
        "message"=>"Comprobante rechazado."

    ]);

}catch(Exception $e){

    $connect->rollback();

    echo json_encode([

        "success"=>false,
        "message"=>$e->getMessage()

    ]);

}

?>