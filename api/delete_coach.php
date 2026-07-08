<?php

header('Content-Type:application/json');

require_once '../php_action/conn_db.php';

//VALIDACION DE ID

if(empty($_POST['id'])){

    echo json_encode([

        'success' => false,
        'message' => 'ID no recibido.'
    ]);

    exit();
}

$id = intval($_POST['id']);

//VERIFICAR EXISTENCIA

$sqlCheck = "
    SELECT id
    FROM coaches
    WHERE id = ?
";

$stmtCheck = $connect->prepare($sqlCheck);
$stmtCheck->bind_param("i",
    $id
);

$stmtCheck->execute();

$resultCheck = $stmtCheck->get_result();

if($resultCheck->num_rows === 0){

    echo json_encode([
        'success' => false,
        'message' => 'Coach no encontrado.'
    ]);

    exit();
}

//DESACTIVAR COACH

$sql = "
    UPDATE coaches
    SET activo = 0
    WHERE id = ?
";

$stmt = $connect->prepare($sql);
$stmt->bind_param("i",
    $id
);

if($stmt->execute()){

    echo json_encode([
        'success' => true,
        'message' => 'Coach eliminado correctamente.'
    ]);
}else{

    echo json_encode([

        'success' => false,
        'message' => 'No fue posible eliminar al coach.'
    ]);
}

$stmt->close();
$stmtCheck->close();

$connect->close();


?>