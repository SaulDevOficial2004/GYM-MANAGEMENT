<?php

$testHost = 'localhost';
$testUser = 'root';
$testPassword = 'root';
$testDatabase = 'profitnessgym_test';

$GLOBALS['testConnect'] = new mysqli(
    $testHost,
    $testUser,
    $testPassword,
    $testDatabase
);

if ($GLOBALS['testConnect']->connect_error) {
    die(
        'Error de conexión a la base de pruebas: '.
        $GLOBALS['testConnect']->connect_error
    );
}

$GLOBALS['testConnect']->set_charset('utf8mb4');
