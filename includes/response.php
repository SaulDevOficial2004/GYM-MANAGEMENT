<?php

declare(strict_types=1);

function jsonResponse(array $data, int $code = 200, int $flags = 0): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, $flags);
    exit();
}
