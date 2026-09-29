<?php

declare(strict_types=1);

/*
 * Migra /uploads a STORAGE_PATH conservando folio/año/mes.
 * - Solo CLI: php bin/migrar_uploads.php
 * - Idempotente: omite archivos que ya existen en destino.
 * - NO toca la base de datos (las rutas guardadas son relativas).
 */

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Solo CLI.\n");
    exit(1);
}

require_once __DIR__ . '/../config/config.php';

$origen = dirname(__DIR__) . '/uploads';
$destino = STORAGE_PATH;

foreach (['comprobantes', 'coaches'] as $carpeta) {
    if (!is_dir($destino . '/' . $carpeta)) {
        mkdir($destino . '/' . $carpeta, 0775, true);
    }
}

$movidos = 0;
$omitidos = 0;
$errores = 0;

$iterador = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($origen, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($iterador as $archivo) {
    /** @var SplFileInfo $archivo */
    if (!$archivo->isFile() || $archivo->getFilename() === '.gitkeep') {
        continue;
    }

    $relativa = substr($archivo->getPathname(), strlen($origen) + 1);
    $destinoFinal = $destino . '/' . $relativa;

    if (file_exists($destinoFinal)) {
        $omitidos++;
        continue;
    }

    $carpetaDestino = dirname($destinoFinal);

    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0775, true);
    }

    if (rename($archivo->getPathname(), $destinoFinal)) {
        $movidos++;
    } else {
        $errores++;
        fwrite(STDERR, "Error moviendo: {$relativa}\n");
    }
}

echo "Movidos: {$movidos}\n";
echo "Omitidos (ya existían): {$omitidos}\n";
echo "Errores: {$errores}\n";
