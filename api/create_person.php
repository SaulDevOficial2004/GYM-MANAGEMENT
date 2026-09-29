<?php

require_once '../php_action/conn_db.php';

require_once __DIR__ . '/../includes/api_auth.php';
require_once __DIR__ . '/../includes/audit.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    apiError('Método no permitido', 405);
}

requireApiRoles([
    'Administrador',
    'Dueño',
    'Recepcionista'
]);

$data = json_decode(file_get_contents("php://input"), true);

$nombre = trim($data['nombre'] ?? '');
$fecha_ini = $data['fecha_ini'] ?? '';
$fecha_fin = $data['fecha_fin'] ?? '';
$membresia_id = intval($data['membresia_id'] ?? 0);

if($nombre === '' || $fecha_ini === '' || $fecha_fin === '' || $membresia_id <= 0){

    jsonResponse([
        "status" => "error",
        "message" => "Todos los datos son obligatorios"
    ]);
}

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
    $resultFolio = $stmtFolio->get_result();
    $folioExiste = $resultFolio->num_rows > 0;
    $stmtFolio->close();

}while($folioExiste);

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
        ?, ?, ?, ?, ?
    )

";

$stmt = $connect->prepare($sql);

$stmt->bind_param(

    "ssssi",

    $nombre,
    $folio,
    $fecha_ini,
    $fecha_fin,
    $membresia_id

);

// TRANSACCION

$connect->begin_transaction();

try{

if($stmt->execute()){

    //ID

    $persona_id = $stmt->insert_id;

    //MEMBRESIA

    $sqlMembresia = "

        SELECT *
        FROM membresias
        WHERE id = ?

    ";

    $stmtMembresia = $connect->prepare($sqlMembresia);

    $stmtMembresia->bind_param(
        "i",
        $membresia_id
    );

    $stmtMembresia->execute();

    $resultMembresia = $stmtMembresia->get_result();

    $membresia = $resultMembresia->fetch_assoc();

    if(!$membresia){

        $connect->rollback();

        jsonResponse([
            "status" => "error",
            "message" => "La membresía no existe"
        ]);
    }

    //USUARIO RESPONSABLE

    $usuario_id = apiCurrentUserId();

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

        if (!registerActivity(

            $connect,
            $usuario_id,
            'CREAR',
            'PERSONAS',
            $persona_id,
            "Registró al cliente {$nombre}"

        )) {
            $connect->rollback();
            apiError('No se pudo completar la operación.', 500);
        }

        $connect->commit();

        jsonResponse([
            "status" => "success",
            "message" => "Persona registrada correctamente",
            "folio" => $folio
        ]);

    }else{

        $connect->rollback();

        jsonResponse([
            "status" => "error",
            "message" => "Error al registrar la venta"
        ]);
    }

}else{

    $connect->rollback();

    jsonResponse([
        "status" => "error",
        "message" => "Error al registrar"
    ]);

}

}catch(Throwable $e){

    $connect->rollback();

    error_log(
        'Error en create_person: ' . $e->getMessage()
    );

    apiError(
        'No se pudo completar la operación.',
        500
    );

}

?>