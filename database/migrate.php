<?php

require_once __DIR__ . '/../Conexion.php';

$migrationsPath = __DIR__ . '/migrations';

// 1️⃣ migraciones ya ejecutadas
$executed = Conexion::buscarVariosRegistro(
    "SELECT migration FROM migrations"
);

$executed = array_column($executed, 'migration');

// 2️⃣ leer archivos
$files = scandir($migrationsPath);

foreach ($files as $file) {

    if ($file === '.' || $file === '..') continue;

    if (in_array($file, $executed)) {
        echo "✔ $file ya ejecutada\n";
        continue;
    }

    require_once $migrationsPath . '/' . $file;

    // nombre de la clase = nombre del archivo sin .php
    $className = pathinfo($file, PATHINFO_FILENAME);

    if (!class_exists($className)) {
        echo "❌ Clase $className no encontrada\n";
        continue;
    }

    echo "▶ Ejecutando $file...\n";

    try {
        Conexion::beginTransaction();

        $migration = new $className();
        $migration->up();

        Conexion::ejecutar(
            "INSERT INTO migrations (migration) VALUES (?)",
            [$file]
        );

        Conexion::commit();

        echo "✅ $file ejecutada\n";
    } catch (Exception $e) {
        Conexion::rollBack();
        echo "🔥 Error en $file: " . $e->getMessage() . "\n";
        break;
    }
}
