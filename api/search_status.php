<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/signed_link.php';
require_once __DIR__ . '/../includes/rate_limit.php';

// Obtener búsqueda
$search = trim($_GET['search'] ?? '');

if(empty($search)){

    jsonResponse([]);
}

if(preg_match('/^CLI-[A-F0-9]{6}$/', $search) !== 1){

    jsonResponse([]);

}

$ipCliente = getClientIp();

if (rateLimit('pub_search_status:' . $ipCliente, 'search', 10, 60)
    || rateLimit('pub_search_status_hora:' . $ipCliente, 'search', 60, 3600)) {
    jsonResponse([
        'success' => false,
        'message' => 'Demasiadas consultas. Intenta más tarde.'
    ], 429);
}

recordLoginAttempt('pub_search_status:' . $ipCliente, false);
recordLoginAttempt('pub_search_status_hora:' . $ipCliente, false);

if (random_int(1, 100) === 1) {
    pruneLoginAttempts();
}

$sql = "

    SELECT
        nombre,
        folio,
        fecha_fin

    FROM personas

    WHERE folio=?

    AND estatus = 1

    ORDER BY nombre ASC

";

$stmt = $connect->prepare($sql);

$stmt->bind_param(
    "s",
    $search,
);

$stmt->execute();

$result = $stmt->get_result();

$personas = [];

while($row = $result->fetch_assoc()){

    $personas[] = $row;

}

jsonResponse($personas);

$stmt->close();
$connect->close();

?>