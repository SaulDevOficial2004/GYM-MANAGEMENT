<?php

session_start();

require_once '../php_action/conn_db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$visitante_id = $data['visitante_id'];

$sql = "
    INSERT INTO visitas(
        visitante_id
    )
    VALUES(?)
";

$stmt = $connect->prepare($sql);
$stmt->bind_param("i",$visitante_id);

if($stmt->execute()){

    $visita_id = $stmt->insert_id;

    // USUARIO
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
    $resultUsuario = $stmtUsuario->get_result();
    $usuario = $resultUsuario->fetch_assoc();

    $usuario_id = $usuario['id'];

    // VISITANTE
    $sqlVisitante = "
        SELECT nombre
        FROM visitantes
        WHERE id = ?
    ";

    $stmtVisitante = $connect->prepare($sqlVisitante);
    $stmtVisitante->bind_param(
        "i",
        $visitante_id
    );

    $stmtVisitante->execute();
    $resultVisitante = $stmtVisitante->get_result();
    $visitante = $resultVisitante->fetch_assoc();

    // VENTA
    $tipo = 'VISITA';
    $descripcion = 'Visita de ' . $visitante['nombre'];
    $total = 50.00;

    $sqlVenta = "
        INSERT INTO ventas
        (
            usuario_id,
            tipo,
            descripcion,
            referencia_id,
            total
        )
        VALUES
        (
            ?, ?, ?, ?, ?
        )
    ";

    $stmtVenta = $connect->prepare($sqlVenta);
    $stmtVenta->bind_param(

        "issid",

        $usuario_id,
        $tipo,
        $descripcion,
        $visita_id,
        $total

    );

    $stmtVenta->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Visita registrada"
    ]);

}else{

    echo json_encode([
        "status" => "error",
        "message" => "Error al registrar visita"
    ]);
}

$stmt->close();
$connect->close();

?>