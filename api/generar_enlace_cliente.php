<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/signed_link.php';
require_once __DIR__ . '/../includes/api_auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    apiError('Método no permitido', 405);
}

requireApiRoles([
    'Administrador',
    'Dueño',
    'Recepcionista'
]);

$rawBody = file_get_contents('php://input');
$data = json_decode($rawBody, true);

$folio = trim((string) ($data['folio'] ?? $_POST['folio'] ?? ''));
$personaId = (int) ($data['persona_id'] ?? $_POST['persona_id'] ?? 0);

if ($folio === '' && $personaId <= 0) {
    apiError('Folio o persona requeridos.', 422);
}

if ($folio !== '' && preg_match('/^CLI-[A-F0-9]{6}$/', $folio) !== 1) {
    apiError('Folio no válido.', 422);
}

if ($folio !== '') {
    $sql = "SELECT folio FROM personas WHERE folio = ? LIMIT 1";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('s', $folio);
} else {
    $sql = "SELECT folio FROM personas WHERE id = ? LIMIT 1";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $personaId);
}

$stmt->execute();
$result = $stmt->get_result();
$persona = $result->fetch_assoc();
$stmt->close();

if (!is_array($persona)) {
    apiError('Cliente no encontrado.', 404);
}

$enlace = generarEnlaceCliente($persona['folio']);

jsonResponse([
    'success' => true,
    'enlace' => $enlace
]);

$connect->close();
