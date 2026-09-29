<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function requireCsrf(): void
{
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if (in_array($method, ['GET', 'HEAD'], true)) {
        return;
    }

    $scriptName = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $excluded = ['login_api.php', 'search_folio.php', 'search_status.php', 'upload_comprobante.php'];

    if (in_array($scriptName, $excluded, true)) {
        return;
    }

    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';

    if (!isset($_SESSION['csrf_token']) || !is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        apiError('Token CSRF inválido', 419);
    }
}
