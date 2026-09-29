<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/signed_link.php';
require_once __DIR__ . '/../includes/rate_limit.php';

$folio = trim($_POST['folio'] ?? '');

if(empty($folio)){
    jsonResponse([

        'success' => false,
        'message' => 'Ingrese un folio.'
    ]);
}

if(preg_match('/^CLI-[A-F0-9]{6}$/', $folio) !== 1){
    jsonResponse([

        'success' => false,
        'message' => 'Folio no válido.'
    ]);
}

$ipCliente = getClientIp();

if (rateLimit('pub_search_folio:' . $ipCliente, 'search', 10, 60)
    || rateLimit('pub_search_folio_hora:' . $ipCliente, 'search', 60, 3600)) {
    jsonResponse([

        'success' => false,
        'message' => 'Demasiadas consultas. Intenta más tarde.'
    ], 429);
}

recordLoginAttempt('pub_search_folio:' . $ipCliente, false);
recordLoginAttempt('pub_search_folio_hora:' . $ipCliente, false);

if (random_int(1, 100) === 1) {
    pruneLoginAttempts();
}

$sql = "
    SELECT
        id,
        nombre,
        folio
    FROM personas
    WHERE folio = ?
    LIMIT 1
";

$stmt = $connect->prepare($sql);

$stmt->bind_param(
    "s",
    $folio
);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows > 0){

    jsonResponse([

        'success' => true,

        'redirect' => generarEnlaceCliente($folio)

    ]);

}else{

    jsonResponse([

        'success' => false,

        'message' =>
        'Folio no encontrado.'

    ]);
}

$stmt->close();
$connect->close();