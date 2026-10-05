<?php
/**
 * Runner de migraciones SQL para Taste (equivalente simple a `php artisan migrate`).
 *
 * Uso:
 *   php bin/migrate.php                     Corre las migraciones pendientes
 *   php bin/migrate.php --pretend           Muestra lo que correría, sin ejecutar
 *   php bin/migrate.php status              Lista migraciones ejecutadas y pendientes
 *   php bin/migrate.php baseline [--until=NOMBRE]
 *                                           Marca migraciones como ejecutadas SIN correrlas
 *                                           (para BDs que ya tienen los cambios aplicados a mano)
 *   php bin/migrate.php seed [--dev] [archivo.sql]
 *                                           Corre los seeders (catálogos). --dev incluye database/seeders/dev
 *   php bin/migrate.php fresh [--seed] [--dev]
 *                                           BORRA toda la BD y la recrea desde el schema (bloqueado en producción)
 *   php bin/migrate.php make nombre_de_la_migracion
 *                                           Crea database/migrations/AAAA_MM_DD_NNNNNN_nombre.sql
 *   php bin/migrate.php schema:dump         Regenera database/schema/schema.sql desde la BD actual
 *   php bin/migrate.php seed:export archivo.sql tabla [--where="..."] [--blank=col1,col2]
 *                                           Exporta filas de una tabla a database/seeders/archivo.sql
 *
 * Compatible con PHP 7.3+. Lee la conexión del .env igual que config.php.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

define('ROOT_PATH', dirname(__DIR__));
define('MIGRATIONS_PATH', ROOT_PATH . '/database/migrations');
define('SEEDERS_PATH', ROOT_PATH . '/database/seeders');
define('SCHEMA_FILE', ROOT_PATH . '/database/schema/schema.sql');
define('MIGRATIONS_TABLE', 'schema_migrations');
// Mismo sql_mode que usa conexion.php (PROD); el schema tiene defaults '0000-00-00'.
define('SQL_MODE', 'IGNORE_SPACE,NO_ENGINE_SUBSTITUTION');

require_once ROOT_PATH . '/env.php';
if (!is_readable(ROOT_PATH . '/.env')) {
    fail('Falta el archivo .env en ' . ROOT_PATH);
}
load_env(ROOT_PATH . '/.env');

// ---------------------------------------------------------------------
// Entrada
// ---------------------------------------------------------------------
$args = array_slice($argv, 1);
$flags = array();
$positional = array();
foreach ($args as $arg) {
    if (strpos($arg, '--') === 0) {
        $parts = explode('=', substr($arg, 2), 2);
        $flags[$parts[0]] = isset($parts[1]) ? $parts[1] : true;
    } else {
        $positional[] = $arg;
    }
}
$command = isset($positional[0]) ? $positional[0] : 'migrate';

$pdo = connect();

switch ($command) {
    case 'migrate':
        cmdMigrate($pdo, !empty($flags['pretend']));
        break;
    case 'status':
        cmdStatus($pdo);
        break;
    case 'baseline':
        cmdBaseline($pdo, isset($flags['until']) ? $flags['until'] : null);
        break;
    case 'seed':
        cmdSeed($pdo, !empty($flags['dev']), isset($positional[1]) ? $positional[1] : null);
        break;
    case 'fresh':
        cmdFresh($pdo, !empty($flags['seed']), !empty($flags['dev']));
        break;
    case 'make':
        cmdMake(isset($positional[1]) ? $positional[1] : null);
        break;
    case 'schema:dump':
        cmdSchemaDump($pdo);
        break;
    case 'seed:export':
        cmdSeedExport(
            $pdo,
            isset($positional[1]) ? $positional[1] : null,
            isset($positional[2]) ? $positional[2] : null,
            isset($flags['where']) ? $flags['where'] : null,
            isset($flags['blank']) ? explode(',', $flags['blank']) : array()
        );
        break;
    default:
        fail("Comando desconocido: $command (ver cabecera de bin/migrate.php)");
}

// ---------------------------------------------------------------------
// Comandos
// ---------------------------------------------------------------------

function cmdMigrate(PDO $pdo, $pretend)
{
    acquireLock($pdo);

    if (!tableExists($pdo, MIGRATIONS_TABLE)) {
        if (countTables($pdo) > 0) {
            fail("La BD '" . dbName() . "' ya tiene tablas pero no tiene " . MIGRATIONS_TABLE . ".\n"
                . "Si los cambios ya están aplicados, corre primero: php bin/migrate.php baseline [--until=NOMBRE]");
        }
        if ($pretend) {
            info('[pretend] BD vacía: se cargaría ' . relPath(SCHEMA_FILE));
            return;
        }
        loadSchema($pdo);
    }

    $pending = pendingMigrations($pdo);
    if (!$pending) {
        info('Nada que migrar.');
        return;
    }

    $batch = nextBatch($pdo);
    foreach ($pending as $name) {
        $file = MIGRATIONS_PATH . '/' . $name;
        if ($pretend) {
            info("[pretend] $name");
            foreach (splitSql(file_get_contents($file)) as $sql) {
                echo '    ' . preg_replace('/\s+/', ' ', mb_substr($sql, 0, 160)) . "\n";
            }
            continue;
        }
        $start = microtime(true);
        echo "Migrando: $name ... ";
        runSqlFile($pdo, $file);
        recordMigration($pdo, $name, $batch);
        echo 'OK (' . round(microtime(true) - $start, 2) . "s)\n";
    }
}

function cmdStatus(PDO $pdo)
{
    if (!tableExists($pdo, MIGRATIONS_TABLE)) {
        info("La BD '" . dbName() . "' no tiene " . MIGRATIONS_TABLE . ' (BD vacía o falta baseline).');
    }
    $ran = ranMigrations($pdo);
    $files = migrationFiles();

    foreach ($files as $name) {
        if (isset($ran[$name])) {
            $row = $ran[$name];
            $changed = $row['checksum'] !== '' && $row['checksum'] !== md5_file(MIGRATIONS_PATH . '/' . $name);
            echo sprintf("  [x] batch %-3s %s%s\n", $row['batch'], $name, $changed ? '  (!) el archivo cambió después de ejecutarse' : '');
        } else {
            echo "  [ ] pendiente  $name\n";
        }
    }
    foreach ($ran as $name => $row) {
        if (!in_array($name, $files, true)) {
            echo "  [?] batch {$row['batch']} $name  (registrada en BD pero el archivo no existe)\n";
        }
    }
}

function cmdBaseline(PDO $pdo, $until)
{
    acquireLock($pdo);
    ensureMigrationsTable($pdo);
    $files = migrationFiles();
    if ($until !== null && !in_array($until, $files, true) && !in_array($until . '.sql', $files, true)) {
        fail("No existe la migración '$until'.");
    }

    $ran = ranMigrations($pdo);
    $batch = nextBatch($pdo);
    $count = 0;
    foreach ($files as $name) {
        if (!isset($ran[$name])) {
            recordMigration($pdo, $name, $batch);
            echo "  marcada: $name\n";
            $count++;
        }
        if ($until !== null && ($name === $until || $name === $until . '.sql')) {
            break;
        }
    }
    info("Baseline: $count migraciones marcadas como ejecutadas (batch $batch), sin correrlas.");
}

function cmdSeed(PDO $pdo, $includeDev, $only)
{
    if ($includeDev) {
        assertNotProduction('seed --dev');
    }
    $files = seederFiles($includeDev);
    if ($only !== null) {
        $files = array_values(array_filter($files, function ($f) use ($only) {
            return basename($f) === $only || basename($f) === $only . '.sql';
        }));
        if (!$files) {
            fail("No existe el seeder '$only'.");
        }
    }
    foreach ($files as $file) {
        echo 'Seed: ' . relPath($file) . ' ... ';
        runSqlFile($pdo, $file);
        echo "OK\n";
    }
}

function cmdFresh(PDO $pdo, $seed, $includeDev)
{
    assertNotProduction('fresh');
    acquireLock($pdo);
    $db = dbName();
    echo "Se van a BORRAR todas las tablas y vistas de '$db'. Escribe el nombre de la BD para confirmar: ";
    $answer = trim((string) fgets(STDIN));
    if ($answer !== $db) {
        fail('Cancelado.');
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach ($pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'")->fetchAll(PDO::FETCH_NUM) as $row) {
        $pdo->exec('DROP VIEW IF EXISTS `' . $row[0] . '`');
    }
    foreach ($pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_NUM) as $row) {
        $pdo->exec('DROP TABLE IF EXISTS `' . $row[0] . '`');
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    info("BD '$db' vaciada.");

    cmdMigrate($pdo, false);
    if ($seed) {
        cmdSeed($pdo, $includeDev, null);
    }
}

function cmdMake($name)
{
    if (!$name) {
        fail('Uso: php bin/migrate.php make nombre_de_la_migracion');
    }
    $slug = trim(preg_replace('/[^a-z0-9]+/', '_', strtolower($name)), '_');
    $prefix = date('Y_m_d');
    $seq = 1;
    foreach (migrationFiles() as $file) {
        if (strpos($file, $prefix . '_') === 0) {
            $seq = max($seq, (int) substr($file, 11, 6) + 1);
        }
    }
    $file = sprintf('%s/%s_%06d_%s.sql', MIGRATIONS_PATH, $prefix, $seq, $slug);
    file_put_contents($file, "-- " . str_replace('_', ' ', $slug) . "\n-- Un cambio por archivo: los ALTER/CREATE de MySQL hacen commit implícito (no hay rollback).\n\n");
    info('Creada: ' . relPath($file));
}

function cmdSchemaDump(PDO $pdo)
{
    $db = dbName();
    $out = array();
    $out[] = '-- Schema completo de Taste. GENERADO con: php bin/migrate.php schema:dump';
    $out[] = '-- No editar a mano: los cambios van en database/migrations/ y luego se regenera este archivo.';
    $out[] = '-- Generado: ' . date('Y-m-d H:i:s') . " desde la BD '$db'";
    $out[] = '';
    $out[] = 'SET FOREIGN_KEY_CHECKS = 0;';
    $out[] = '';

    $tables = array_filter($pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_NUM), function ($row) {
        return $row[0] !== MIGRATIONS_TABLE; // la crea ensureMigrationsTable()
    });
    foreach ($tables as $row) {
        $create = $pdo->query('SHOW CREATE TABLE `' . $row[0] . '`')->fetch(PDO::FETCH_NUM);
        $sql = preg_replace('/ AUTO_INCREMENT=\d+/', '', $create[1]);
        $out[] = $sql . ';';
        $out[] = '';
    }

    $views = array();
    foreach ($pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'")->fetchAll(PDO::FETCH_NUM) as $row) {
        $create = $pdo->query('SHOW CREATE VIEW `' . $row[0] . '`')->fetch(PDO::FETCH_NUM);
        $sql = preg_replace('/^CREATE .*? VIEW /', 'CREATE OR REPLACE VIEW ', $create[1]);
        // Quita el prefijo de la BD de origen para que el schema sirva con cualquier nombre de BD
        $views[$row[0]] = str_replace('`' . $db . '`.', '', $sql);
    }
    foreach (sortViewsByDependency($views) as $sql) {
        $out[] = $sql . ';';
        $out[] = '';
    }

    $others = $pdo->query("SELECT COUNT(*) FROM information_schema.routines WHERE routine_schema = DATABASE()")->fetchColumn()
        + $pdo->query("SELECT COUNT(*) FROM information_schema.triggers WHERE trigger_schema = DATABASE()")->fetchColumn();
    if ($others > 0) {
        warn("La BD tiene $others procedimientos/triggers que NO se incluyen en el schema.");
    }

    $out[] = 'SET FOREIGN_KEY_CHECKS = 1;';
    $out[] = '';
    $out[] = '-- Migraciones ya contenidas en este schema';
    foreach (array_keys(ranMigrations($pdo)) as $name) {
        $out[] = 'INSERT INTO `' . MIGRATIONS_TABLE . '` (`migration`, `batch`, `checksum`) VALUES ('
            . $pdo->quote($name) . ', 0, ' . $pdo->quote((string) @md5_file(MIGRATIONS_PATH . '/' . $name)) . ');';
    }
    $out[] = '';

    if (!is_dir(dirname(SCHEMA_FILE))) {
        mkdir(dirname(SCHEMA_FILE), 0775, true);
    }
    file_put_contents(SCHEMA_FILE, implode("\n", $out));
    info(sprintf('Schema escrito en %s (%d tablas, %d vistas).', relPath(SCHEMA_FILE), count($tables), count($views)));
}

function cmdSeedExport(PDO $pdo, $file, $table, $where, array $blank)
{
    if (!$file || !$table) {
        fail('Uso: php bin/migrate.php seed:export archivo.sql tabla [--where="..."] [--blank=col1,col2]');
    }
    if (!tableExists($pdo, $table)) {
        fail("No existe la tabla '$table'.");
    }
    if (substr($file, -4) !== '.sql') {
        $file .= '.sql';
    }
    $path = SEEDERS_PATH . '/' . $file;
    if (!is_dir(dirname($path))) {
        mkdir(dirname($path), 0775, true);
    }

    $rows = $pdo->query('SELECT * FROM `' . $table . '`' . ($where ? ' WHERE ' . $where : ''))->fetchAll(PDO::FETCH_ASSOC);
    $chunk = array();
    $sql = "\n-- $table" . ($where ? " WHERE $where" : '') . ' (' . count($rows) . " filas)\n";
    foreach ($rows as $i => $row) {
        $values = array();
        foreach ($row as $col => $value) {
            if (in_array($col, $blank, true)) {
                $value = '';
            }
            $values[] = $value === null ? 'NULL' : $pdo->quote($value);
        }
        $chunk[] = '(' . implode(', ', $values) . ')';
        if (count($chunk) === 200 || $i === count($rows) - 1) {
            $cols = '`' . implode('`, `', array_keys($row)) . '`';
            $sql .= "INSERT IGNORE INTO `$table` ($cols) VALUES\n    " . implode(",\n    ", $chunk) . ";\n";
            $chunk = array();
        }
    }

    file_put_contents($path, $sql, FILE_APPEND);
    info(count($rows) . " filas de $table agregadas a " . relPath($path));
}

// ---------------------------------------------------------------------
// Migraciones
// ---------------------------------------------------------------------

function migrationFiles()
{
    $files = array_map('basename', glob(MIGRATIONS_PATH . '/*.sql'));
    sort($files, SORT_STRING);
    return $files;
}

function seederFiles($includeDev)
{
    $files = glob(SEEDERS_PATH . '/*.sql');
    sort($files, SORT_STRING);
    if ($includeDev) {
        $dev = glob(SEEDERS_PATH . '/dev/*.sql');
        sort($dev, SORT_STRING);
        $files = array_merge($files, $dev);
    }
    return $files;
}

function ranMigrations(PDO $pdo)
{
    if (!tableExists($pdo, MIGRATIONS_TABLE)) {
        return array();
    }
    $ran = array();
    $rows = $pdo->query('SELECT migration, batch, checksum FROM `' . MIGRATIONS_TABLE . '` ORDER BY migration')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $ran[$row['migration']] = $row;
    }
    return $ran;
}

function pendingMigrations(PDO $pdo)
{
    $ran = ranMigrations($pdo);
    return array_values(array_filter(migrationFiles(), function ($name) use ($ran) {
        return !isset($ran[$name]);
    }));
}

function nextBatch(PDO $pdo)
{
    return (int) $pdo->query('SELECT COALESCE(MAX(batch), 0) + 1 FROM `' . MIGRATIONS_TABLE . '`')->fetchColumn();
}

function ensureMigrationsTable(PDO $pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS `' . MIGRATIONS_TABLE . '` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `migration` VARCHAR(255) NOT NULL,
        `batch` INT NOT NULL,
        `checksum` CHAR(32) NOT NULL DEFAULT \'\',
        `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_migration` (`migration`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
}

function recordMigration(PDO $pdo, $name, $batch)
{
    $st = $pdo->prepare('INSERT INTO `' . MIGRATIONS_TABLE . '` (migration, batch, checksum) VALUES (?, ?, ?)');
    $st->execute(array($name, $batch, md5_file(MIGRATIONS_PATH . '/' . $name)));
}

function loadSchema(PDO $pdo)
{
    if (!is_readable(SCHEMA_FILE)) {
        warn('No existe ' . relPath(SCHEMA_FILE) . '; se corren las migraciones sobre la BD vacía.');
        ensureMigrationsTable($pdo);
        return;
    }
    echo 'BD vacía: cargando ' . relPath(SCHEMA_FILE) . ' ... ';
    ensureMigrationsTable($pdo);
    runSqlFile($pdo, SCHEMA_FILE);
    echo "OK\n";
}

// ---------------------------------------------------------------------
// Ejecución de SQL
// ---------------------------------------------------------------------

function runSqlFile(PDO $pdo, $file)
{
    $statements = splitSql(file_get_contents($file));
    foreach ($statements as $i => $sql) {
        try {
            $st = $pdo->query($sql);
            if ($st) {
                $st->closeCursor();
            }
        } catch (PDOException $e) {
            echo "\n";
            fail(sprintf(
                "Falló la sentencia %d de %d en %s:\n%s\n\nSQL: %s\n\n"
                . "Ojo: las sentencias anteriores de este archivo YA se aplicaron (MySQL no revierte DDL).",
                $i + 1,
                count($statements),
                relPath($file),
                $e->getMessage(),
                mb_substr($sql, 0, 500)
            ));
        }
    }
}

/**
 * Divide un archivo SQL en sentencias. Respeta strings, identificadores con backticks,
 * comentarios (--, #, /* *\/) y la directiva DELIMITER (procedimientos/triggers).
 */
function splitSql($content)
{
    $statements = array();
    $delimiter = ';';
    $buffer = '';
    $len = strlen($content);
    $i = 0;

    while ($i < $len) {
        // DELIMITER al inicio de línea
        if (($i === 0 || $content[$i - 1] === "\n") && preg_match('/\GDELIMITER\s+(\S+)[^\n]*\n?/Ai', $content, $m, 0, $i)) {
            $delimiter = $m[1];
            $i += strlen($m[0]);
            continue;
        }

        $char = $content[$i];
        $next = $i + 1 < $len ? $content[$i + 1] : '';

        // Comentarios de línea (no se agregan al buffer)
        if (($char === '-' && $next === '-' && ($i + 2 >= $len || ctype_space($content[$i + 2]))) || $char === '#') {
            $end = strpos($content, "\n", $i);
            $i = $end === false ? $len : $end;
            continue;
        }
        // Comentarios de bloque (los /*! ... */ ejecutables de MySQL se conservan)
        if ($char === '/' && $next === '*') {
            $end = strpos($content, '*/', $i + 2);
            $end = $end === false ? $len : $end + 2;
            if ($i + 2 < $len && $content[$i + 2] === '!') {
                $buffer .= substr($content, $i, $end - $i);
            }
            $i = $end;
            continue;
        }
        // Strings e identificadores
        if ($char === "'" || $char === '"' || $char === '`') {
            $j = $i + 1;
            while ($j < $len) {
                if ($content[$j] === '\\' && $char !== '`') {
                    $j += 2;
                    continue;
                }
                if ($content[$j] === $char) {
                    if ($j + 1 < $len && $content[$j + 1] === $char) {
                        $j += 2;
                        continue;
                    }
                    break;
                }
                $j++;
            }
            $buffer .= substr($content, $i, $j - $i + 1);
            $i = $j + 1;
            continue;
        }
        // Fin de sentencia
        if (substr($content, $i, strlen($delimiter)) === $delimiter) {
            if (trim($buffer) !== '') {
                $statements[] = trim($buffer);
            }
            $buffer = '';
            $i += strlen($delimiter);
            continue;
        }

        $buffer .= $char;
        $i++;
    }

    if (trim($buffer) !== '') {
        $statements[] = trim($buffer);
    }
    return $statements;
}

function sortViewsByDependency(array $views)
{
    $sorted = array();
    $remaining = $views;
    while ($remaining) {
        $progress = false;
        foreach ($remaining as $name => $sql) {
            $dependsOnPending = false;
            foreach (array_keys($remaining) as $other) {
                if ($other !== $name && strpos($sql, '`' . $other . '`') !== false) {
                    $dependsOnPending = true;
                    break;
                }
            }
            if (!$dependsOnPending) {
                $sorted[] = $sql;
                unset($remaining[$name]);
                $progress = true;
            }
        }
        if (!$progress) {
            // Dependencia circular (no debería pasar): se agregan tal cual
            return array_merge($sorted, array_values($remaining));
        }
    }
    return $sorted;
}

// ---------------------------------------------------------------------
// Utilidades
// ---------------------------------------------------------------------

function connect()
{
    $host = env('DB_HOST', '127.0.0.1');
    $port = env('DB_PORT', '3306');
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=" . dbName() . ';charset=utf8mb4',
            env('DB_USERNAME', 'root'),
            env('DB_PASSWORD', ''),
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
    } catch (PDOException $e) {
        fail('No se pudo conectar a la BD: ' . $e->getMessage());
    }
    $pdo->exec("SET SESSION sql_mode = '" . SQL_MODE . "'");
    return $pdo;
}

function dbName()
{
    return env('DB_DATABASE', 'jc_taste');
}

function acquireLock(PDO $pdo)
{
    $lock = 'taste_migrate_' . dbName();
    if ((int) $pdo->query('SELECT GET_LOCK(' . $pdo->quote($lock) . ', 10)')->fetchColumn() !== 1) {
        fail('Otro proceso está migrando esta BD (no se obtuvo el lock).');
    }
}

function assertNotProduction($what)
{
    if (strtolower((string) env('APP_ENV', 'development')) === 'production') {
        fail("'$what' está bloqueado con APP_ENV=production.");
    }
}

function tableExists(PDO $pdo, $table)
{
    $st = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
    $st->execute(array($table));
    return (int) $st->fetchColumn() > 0;
}

function countTables(PDO $pdo)
{
    return (int) $pdo->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()')->fetchColumn();
}

function relPath($path)
{
    return ltrim(str_replace('\\', '/', substr($path, strlen(ROOT_PATH))), '/');
}

function info($msg)
{
    echo $msg . "\n";
}

function warn($msg)
{
    fwrite(STDERR, "AVISO: $msg\n");
}

function fail($msg)
{
    fwrite(STDERR, "ERROR: $msg\n");
    exit(1);
}
