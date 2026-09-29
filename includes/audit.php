<?php

function registerActivity(
    mysqli $connect,
    int $usuario_id,
    string $accion,
    string $modulo,
    ?int $registro_id,
    string $descripcion,
    ?string $motivo = null
): bool {

    $sql = "

        INSERT INTO bitacora_actividad (

            usuario_id,
            accion,
            modulo,
            registro_id,
            descripcion,
            motivo

        ) VALUES (?, ?, ?, ?, ?, ?)

    ";

    $stmt = $connect->prepare($sql);

    if (!$stmt) {

        return false;

    }

    $stmt->bind_param(

        'ississ',

        $usuario_id,
        $accion,
        $modulo,
        $registro_id,
        $descripcion,
        $motivo

    );

    $result = $stmt->execute();

    $stmt->close();

    return $result;
}