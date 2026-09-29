<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function getClientIp(): string
{
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    return $ip;
}

function rateLimit(string $identificador, string $accion, int $maxIntentos, int $ventanaSegundos): bool
{
    global $connect;

    $ip = inet_pton(getClientIp());
    if ($ip === false) {
        $ip = inet_pton('127.0.0.1');
    }

    $sql = "
        SELECT COUNT(*) AS total
        FROM intentos_login
        WHERE identificador = ?
          AND creado_en >= DATE_SUB(NOW(), INTERVAL ? SECOND)
          AND exitoso = 0
    ";

    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('si', $identificador, $ventanaSegundos);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = (int) $result->fetch_assoc()['total'];
    $stmt->close();

    return $count >= $maxIntentos;
}

function recordLoginAttempt(string $identificador, bool $exitoso): void
{
    global $connect;

    $ip = inet_pton(getClientIp());
    if ($ip === false) {
        $ip = inet_pton('127.0.0.1');
    }

    $sql = "INSERT INTO intentos_login (identificador, ip, exitoso) VALUES (?, ?, ?)";
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        return;
    }

    $stmt->bind_param('sbi', $identificador, $ip, $exitoso);
    $stmt->execute();
    $stmt->close();
}

function clearFailedLoginAttempts(string $identificador): void
{
    global $connect;

    $sql = "DELETE FROM intentos_login WHERE identificador = ? AND exitoso = 0";
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        return;
    }

    $stmt->bind_param('s', $identificador);
    $stmt->execute();
    $stmt->close();
}

function pruneLoginAttempts(): void
{
    global $connect;

    $sql = "DELETE FROM intentos_login WHERE creado_en < DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $stmt = $connect->prepare($sql);
    if (!$stmt) {
        return;
    }
    $stmt->execute();
    $stmt->close();
}
