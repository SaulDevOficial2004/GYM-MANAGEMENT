<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/signed_link.php';
require_once __DIR__ . '/../includes/api_auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    apiError('Método no permitido', 405);
}

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(404);
    exit();
}

$folioFirmado = null;

if (isset($_GET['f'], $_GET['e'], $_GET['s'])) {
    $enlace = validarEnlaceCliente();
    $folioFirmado = $enlace['folio'];
} else {
    requireApiRoles([
        'Administrador',
        'Dueño',
        'Recepcionista'
    ]);
}

$sql = "SELECT archivo, folio_cliente FROM comprobantes_pago WHERE id = ? LIMIT 1";
$stmt = $connect->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$comprobante = $result->fetch_assoc();
$stmt->close();

if (!is_array($comprobante)) {
    http_response_code(404);
    exit();
}

if ($folioFirmado !== null && $comprobante['folio_cliente'] !== $folioFirmado) {
    http_response_code(403);
    exit();
}

$baseReal = realpath(STORAGE_PATH);

if ($baseReal === false) {
    http_response_code(500);
    exit();
}

$rutaReal = realpath($baseReal . '/' . $comprobante['archivo']);

if ($rutaReal === false || strpos($rutaReal, $baseReal) !== 0 || !is_file($rutaReal)) {
    http_response_code(404);
    exit();
}

$extension = strtolower(pathinfo($rutaReal, PATHINFO_EXTENSION));

$tipos = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png'
];

if (!isset($tipos[$extension])) {
    http_response_code(403);
    exit();
}

header('Content-Type: ' . $tipos[$extension]);
header('Content-Disposition: inline');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Content-Length: ' . filesize($rutaReal));

readfile($rutaReal);
exit();
