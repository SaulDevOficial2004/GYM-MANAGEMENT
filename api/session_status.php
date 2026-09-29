<?php

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

require_once __DIR__ . '/../includes/api_auth.php';

requireApiLogin();

jsonResponse([
    'success' => true,
    'active' => true
]);
