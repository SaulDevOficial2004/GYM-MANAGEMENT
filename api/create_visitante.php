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

require_once '../php_action/conn_db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$nombre = trim($data['nombre']);

$sql = "
    INSERT INTO visitantes(
        nombre
    )
    VALUES(?)
";

$stmt = $connect->prepare($sql);
$stmt->bind_param("s",$nombre);

if($stmt->execute()){

    $visitante_id = $connect->insert_id;

    $sql_visita = "
        INSERT INTO visitas(
            visitante_id
        )
        VALUES(?)
    ";

    $stmt_visita = $connect->prepare($sql_visita);
    $stmt_visita->bind_param("i",$visitante_id);
    $stmt_visita->execute();

    $visita_id = $connect->insert_id;

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

    // VENTA
    $tipo = 'VISITA';
    $descripcion = 'Visita de ' . $nombre;
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

    jsonResponse([
        "status" => "success",
        "message" => "Visitante registrado correctamente."
    ]);

}else{

    jsonResponse([
        "status" => "error",
        "message" => "No se pudo registrar la visita."
    ]);

}

$stmt->close();
$connect->close();

?>