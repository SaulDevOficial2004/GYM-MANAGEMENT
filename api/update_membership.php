<?php

session_start();

header('Content-Type: application/json');

require_once "../php_action/conn_db.php";

if(!isset($_SESSION['telefono'])){
    
    echo json_encode([
        
        "status" => "error",
        "message" => "Sesión expirada"
    ]);

    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];

$fecha_ini = $data['fecha_ini'];
$fecha_fin = $data['fecha_fin'];
$membresia_id = $data['membresia_id'];

$sql = "
    UPDATE personas
    SET fecha_ini = ?, fecha_fin = ?
    WHERE id = ?
";

$stmt = $connect->prepare($sql);

$stmt->bind_param("ssi",
    $fecha_ini,
    $fecha_fin,
    $id
);

if($stmt->execute()){

    // OBTENER PERSONA
    $sqlPersona = "
        SELECT
            personas.nombre,
            personas.membresia_id,
            membresias.nombre AS membresia_nombre,
            membresias.precio,
            membresias.promocion,
            membresias.precio_promocion
        FROM personas
        INNER JOIN membresias
        ON membresias.id = personas.membresia_id
        WHERE personas.id = ?
    ";

    $stmtPersona = $connect->prepare($sqlPersona);
    $stmtPersona->bind_param(
        "i",
        $id
    );


    $stmtPersona->execute();
    $resultPersona = $stmtPersona->get_result();
    $persona = $resultPersona->fetch_assoc();

    //OBTENER USUARIO LOGUEADO
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

    //CALCULAR TOTAL
    $total = $persona['precio'];

    if($persona['promocion'] == 1 && !empty($persona['precio_promocion'])){

        $total = $persona['precio_promocion'];
    }

    //REGISTRAR VENTA
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
    $tipo = 'MEMBRESIA';
    $descripcion = 'Renovación ' . $persona['membresia_nombre'];
    $stmtVenta->bind_param(

        "issid",

        $usuario_id,
        $tipo,
        $descripcion,
        $id,
        $total
    );

    //EJECUTAR VENTA
    if($stmtVenta->execute()){

        echo json_encode([

            "status" => "success",
            "message" => "Membresía actualizada correctamente"

        ]);

    }else{

        echo json_encode([

            "status" => "error",
            "message" => "Error al registrar la venta"

        ]);
    }

}else{

    echo json_encode([
        "status" => "error",
        "message" => "Error al actualizar"
    ]);
}

?>