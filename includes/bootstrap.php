<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/response.php';

$docRoot = realpath(__DIR__ . '/..');
$storageReal = realpath(STORAGE_PATH);

if ($storageReal === false) {
    $normalizado = str_replace('\\', '/', STORAGE_PATH);
    $partes = [];

    foreach (explode('/', $normalizado) as $segmento) {
        if ($segmento === '' || $segmento === '.') {
            continue;
        }

        if ($segmento === '..') {
            array_pop($partes);
            continue;
        }

        $partes[] = $segmento;
    }

    $storageReal = implode('/', $partes);
}

if (
    $docRoot !== false
    && stripos(rtrim($storageReal, '/') . '/', rtrim(str_replace('\\', '/', $docRoot), '/') . '/') === 0
) {
    error_log('GMS: STORAGE_PATH no puede estar dentro del webroot: ' . STORAGE_PATH);

    if (php_sapi_name() === 'cli') {
        fwrite(STDERR, "GMS: STORAGE_PATH no puede estar dentro del webroot.\n");
        exit(1);
    }

    http_response_code(500);
    echo 'Error de configuración.';
    exit();
}

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$connect = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
mysqli_set_charset($connect, 'utf8mb4');

if (APP_ENV === 'production') {
    if (!is_dir(STORAGE_PATH . '/logs')) {
        mkdir(STORAGE_PATH . '/logs', 0775, true);
    }

    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', STORAGE_PATH . '/logs/php-error.log');
} else {
    ini_set('display_errors', '1');
}

$isLocal = str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost')
    || ($_SERVER['REMOTE_ADDR'] ?? '') === '127.0.0.1'
    || ($_SERVER['REMOTE_ADDR'] ?? '') === '::1';

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => !$isLocal,
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_name('GMSSESSID');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
