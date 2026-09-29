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

//VALIDACIONES

if(empty($_POST['id'])){

    jsonResponse([

        'success' => false,
        'message' => 'ID inválido.'
    ]);

}

$id = intval($_POST['id']);

//ELIMINAR


$sql = "
    DELETE FROM membresias
    WHERE id = ?
";

$stmt = $connect->prepare($sql);
$stmt->bind_param(
    "i",
    $id
);
if($stmt->execute()){

    jsonResponse([

        'success' => true,
        'message' => 'Membresía eliminada correctamente.'
    ]);

}else{

    jsonResponse([

        'success' => false,
        'message' => 'No fue posible eliminar la membresía.'

    ]);

}

$stmt->close();
$connect->close();

?>