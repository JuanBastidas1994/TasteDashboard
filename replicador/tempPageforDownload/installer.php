<?php
$token = $_GET['token'] ?? '';
$expectedToken = 'clave-super-secreta';

if ($token !== $expectedToken) {
    http_response_code(403);
    exit('Token inválido.');
}

// Ruta temporal para guardar el ZIP
$zipUrl = $_GET['zip'] ?? '';
$zipPath = 'temp.zip';

// Descargar el zip
file_put_contents($zipPath, fopen($zipUrl, 'r'));

// Descomprimir
$zip = new ZipArchive;
if ($zip->open($zipPath) === TRUE) {
    $zip->extractTo(__DIR__);
    $zip->close();
    unlink($zipPath);
    echo 'Instalación completa.';
} else {
    echo 'Error al descomprimir.';
}