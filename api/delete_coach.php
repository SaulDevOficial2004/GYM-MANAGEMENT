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

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../php_action/conn_db.php';

//VALIDACION DE ID

if(empty($_POST['id'])){

    jsonResponse([

        'success' => false,
        'message' => 'ID no recibido.'
    ]);
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

    jsonResponse([
        'success' => false,
        'message' => 'Coach no encontrado.'
    ]);
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

    jsonResponse([
        'success' => true,
        'message' => 'Coach eliminado correctamente.'
    ]);
}else{

    jsonResponse([

        'success' => false,
        'message' => 'No fue posible eliminar al coach.'
    ]);
}

$stmt->close();
$stmtCheck->close();

$connect->close();


?>