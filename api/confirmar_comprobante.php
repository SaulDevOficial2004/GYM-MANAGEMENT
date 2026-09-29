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
require_once __DIR__ . '/../includes/audit.php';

//VALIDAR SESION

if(!isset($_SESSION['telefono'])){

    jsonResponse([
        "success"=>false,
        "message"=>"Sesión expirada."
    ]);
}

//VALIDAR ID

if(empty($_POST['id'])){

    jsonResponse([
        "success"=>false,
        "message"=>"Comprobante inválido."
    ]);
}

if(empty($_POST['id']) || empty($_POST['membresia_id'])){

    jsonResponse([
        "success"=>false,
        "message"=>"Datos inválidos."
    ]);
}

$id = intval($_POST['id']);
$membresia_id = intval($_POST['membresia_id']);

//OBTENER LOGUEADO

$telefono = $_SESSION['telefono'];

$sqlUsuario = "
    SELECT id
    FROM usuarios
    WHERE telefono = ?
    LIMIT 1
";

$stmt = $connect->prepare($sqlUsuario);

$stmt->bind_param(
    "s",
    $telefono
);

$stmt->execute();

$resultUsuario = $stmt->get_result();

if($resultUsuario->num_rows==0){

    jsonResponse([
        "success"=>false,
        "message"=>"Usuario no encontrado."
    ]);
}

$usuario = $resultUsuario->fetch_assoc();

$usuario_id = $usuario['id'];

//INICIAR TRANSACCION

$connect->begin_transaction();

try{

    //BLOQUEAR COMPROBANTE

    $sql="

        SELECT

            cp.*,
            p.nombre,
            p.fecha_ini,
            p.fecha_fin,
            p.estatus,
            p.membresia_id

        FROM comprobantes_pago cp
        INNER JOIN personas p
        ON p.id = cp.persona_id
        WHERE cp.id = ?
        FOR UPDATE

    ";

    $stmt=$connect->prepare($sql);
    $stmt->bind_param("i",

        $id

    );

    $stmt->execute();
    $result=$stmt->get_result();

    if($result->num_rows==0){

        throw new Exception(

            "Comprobante no encontrado."

        );

    }

    $comprobante = $result->fetch_assoc();

    $sqlMembresia = "

        SELECT *
        FROM membresias
        WHERE id = ?
        AND activo = 1
        LIMIT 1

    ";

    $stmtMembresia = $connect->prepare($sqlMembresia);
    $stmtMembresia->bind_param("i",

        $membresia_id

    );

    $stmtMembresia->execute();
    $resultMembresia = $stmtMembresia->get_result();

    if($resultMembresia->num_rows == 0){

        throw new Exception("La membresía seleccionada no existe.");

    }

    $membresia = $resultMembresia->fetch_assoc();

    //VALIDAR STATUS

    if(

    $comprobante['status'] != 'PENDIENTE'){

        throw new Exception(

            "Este comprobante ya fue procesado."

        );

    }

    //CALCULAR FECHAS

    $renovacion = \GMS\Domain\MembresiaRenovacion::calcular(
        $comprobante['fecha_fin'],
        (int) $membresia['dias']
    );

    $fechaInicioSQL = $renovacion['inicio'];
    $fechaFinSQL = $renovacion['fin'];

    //CALCULAR TOTAL

    $total = $membresia['precio'];

    if($membresia['promocion'] == 1 && !empty($comprobante['precio_promocion'])){

        $total = $membresia['precio_promocion'];

    }

    //REGISTRAR VENTA

    $sqlVenta="

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
    $descripcion = "Renovación por transferencia - " . $membresia['nombre'];
    $referencia_id = $comprobante['persona_id'];

    $stmtVenta->bind_param("issid",

    $usuario_id,
    $tipo,
    $descripcion,
    $referencia_id,
    $total

    );

    if(!$stmtVenta->execute()){

        throw new Exception(

            "No fue posible registrar la venta."

        );
    }

    $venta_id = $connect->insert_id;

    //HISTORIAL MEMBRESIA

    $sqlHistorial="

        INSERT INTO membresias_cliente
        (

            persona_id,
            membresia_id,
            venta_id,
            fecha_inicio,
            fecha_fin,
            precio_pagado

        )

        VALUES

        (
            ?,?,?,?,?,?
        )

    ";

    $stmtHistorial = $connect->prepare($sqlHistorial);

    $stmtHistorial->bind_param("iiissd",

        $comprobante['persona_id'],
        $membresia_id,
        $venta_id,
        $fechaInicioSQL,
        $fechaFinSQL,
        $total

    );

    if(!$stmtHistorial->execute()){

        throw new Exception(

            "No fue posible registrar el historial."

        );
    }

    //ACTUALIZAR PERSONA

    $sqlPersona="

        UPDATE personas SET

            membresia_id=?,
            fecha_ini=?,
            fecha_fin=?,
            estatus=1

        WHERE id=?

    ";

    $stmtPersona = $connect->prepare($sqlPersona);

    $stmtPersona->bind_param("issi",

        $membresia_id,
        $fechaInicioSQL,
        $fechaFinSQL,
        $comprobante['persona_id']

    );

    if(!$stmtPersona->execute()){

        throw new Exception(

            "No fue posible actualizar la persona."

        );
    }

    //ACTUALIZAR COMPROBANTE

    $sqlComp="

        UPDATE comprobantes_pago SET

            status='CONFIRMADO',
            fecha_revision=NOW(),
            revisado_por=?

        WHERE id=?

    ";

    $stmtComp = $connect->prepare($sqlComp);

    $stmtComp->bind_param("ii",

        $usuario_id,
        $id

    );

    if(!$stmtComp->execute()){

        throw new Exception(

            "No fue posible actualizar el comprobante."

        );
    }

    //BITACORA

    if (!registerActivity(

        $connect,
        $usuario_id,
        'EDITAR',
        'COMPROBANTES',
        $id,
        "Confirmó comprobante #" . $id . " (" . $comprobante['folio_cliente'] . ")"

    )) {
        $connect->rollback();
        apiError('No se pudo completar la operación.', 500);
    }

    //COMMIT

    $connect->commit();

    jsonResponse([

        "success"=>true,
        "message"=>"Pago confirmado correctamente."

    ]);

    }catch(Throwable $e){

        $connect->rollback();

        error_log(
            'Error en confirmar_comprobante: ' . $e->getMessage()
        );

        $mensaje = $e instanceof Exception
            ? $e->getMessage()
            : 'No se pudo completar la operación.';

        echo json_encode([

            "success"=>false,

            "message"=>$mensaje

        ]);

    }

    $stmt->close();

    $connect->close();

?>