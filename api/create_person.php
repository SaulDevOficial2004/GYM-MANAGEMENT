<?php

session_start();

header('Content-Type: application/json');

require_once '../php_action/conn_db.php';

if(!isset($_SESSION['telefono'])){

    echo json_encode([
            "status" => "error",
            "message" => "Sesión expirada"
        ]);

    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'];
$fecha_ini = $data['fecha_ini'];
$fecha_fin = $data['fecha_fin'];
$membresia_id = $data['membresia_id'];

//ACTUALIZACION DEL FOLIO ALEATORIO
function generarFolio(){

    return 'CLI-' .
    strtoupper(
        substr(
            bin2hex(random_bytes(3)),
            0,
            6
        )
    );
}

do{

    $folio = generarFolio();

    $sqlFolio = "
        SELECT id
        FROM personas
        WHERE folio = ?
    ";

    $stmtFolio = $connect->prepare($sqlFolio);

    $stmtFolio->bind_param(
        "s",
        $folio
    );

    $stmtFolio->execute();

    $resultFolio =
        $stmtFolio->get_result();

}while(
    $resultFolio->num_rows > 0
);

//INSERSION CON FOLIO ALEATORIO CREADO
$sql = "
    INSERT INTO personas
    (
        nombre,
        folio,
        fecha_ini,
        fecha_fin,
        membresia_id
    )
    VALUES
    (
        ?,?,?,?,?
    )
";

$stmt = $connect->prepare($sql);

$stmt->bind_param("ssssi",
    $nombre,
    $folio,
    $fecha_ini,
    $fecha_fin,
    $membresia_id
);


if($stmt->execute()){
    //ID
    $persona_id = $stmt->insert_id;

    //MEMBRESIA
    $sqlMembresia = "
        SELECT *
        FROM membresias
        WHERE id = ?
    ";

    $stmtMembresia =$connect->prepare($sqlMembresia);
    $stmtMembresia->bind_param(
        "i",
        $membresia_id
    );

    $stmtMembresia->execute();
    $resultMembresia = $stmtMembresia->get_result();
    $membresia = $resultMembresia->fetch_assoc();

    if(!$membresia){

        echo json_encode([
            "status" => "error",
            "message" => "La membresía no existe"
        ]);

        exit();
    }

    //USUARIO
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

    if(!$usuario){

        echo json_encode([
            "status" => "error",
            "message" => "Usuario no encontrado"
        ]);

        exit();
    }

    $usuario_id = $usuario['id'];

    //VENTA
    $total = $membresia['precio'];

    if($membresia['promocion'] == 1 && !empty($membresia['precio_promocion'])){

        $total = $membresia['precio_promocion'];
    }

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
    $descripcion = $membresia['nombre'];
    $stmtVenta->bind_param(

            "issid",

        $usuario_id,
        $tipo,
        $descripcion,
        $persona_id,
        $total
    );

    if($stmtVenta->execute()){

        echo json_encode([
            "status" => "success",
            "message" => "Persona registrada correctamente",
            "folio" => $folio
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
        "message" => "Error al registrar"
    ]);
}

?>