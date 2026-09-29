<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function portalBaseUrl(): string
{
    if (APP_URL !== '') {
        return rtrim(APP_URL, '/') . '/transferencias_cliente.php';
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'gym.saul-dev.com';

    return $scheme . '://' . $host . '/transferencias_cliente.php';
}

function generarEnlaceCliente(string $folio, int $diasVigencia = 30): string
{
    $expira = time() + $diasVigencia * 86400;
    $firma = hash_hmac('sha256', $folio . '|' . $expira, APP_KEY);

    return portalBaseUrl()
        . '?f=' . urlencode($folio)
        . '&e=' . $expira
        . '&s=' . urlencode($firma);
}

function validarEnlaceCliente(bool $json = false): array
{
    $folio = $_GET['f'] ?? $_POST['f'] ?? '';
    $expira = $_GET['e'] ?? $_POST['e'] ?? '';
    $firma = $_GET['s'] ?? $_POST['s'] ?? '';

    $valido = is_string($folio)
        && is_string($expira)
        && is_string($firma)
        && preg_match('/^CLI-[A-F0-9]{6}$/', $folio) === 1
        && ctype_digit((string) $expira)
        && (int) $expira >= time()
        && hash_equals(hash_hmac('sha256', $folio . '|' . $expira, APP_KEY), $firma);

    if (!$valido) {
        http_response_code(403);

        if ($json) {
            header('Content-Type: application/json; charset=utf-8');

            echo json_encode([
                'success' => false,
                'message' => 'Enlace no válido o expirado.'
            ]);
        } else {
            echo 'Enlace no válido o expirado.';
        }

        exit();
    }

    return [
        'folio' => $folio,
        'expira' => (int) $expira,
        'firma' => $firma
    ];
}
