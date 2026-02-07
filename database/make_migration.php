<?php

if (php_sapi_name() !== 'cli') {
    die("❌ Solo CLI\n");
}

if (empty($argv[1])) {
    die("Uso: php make_migration.php create_users_table\n");
}

$name = $argv[1];

// timestamp estilo Laravel
$timestamp = date('Y_m_d_His');
$className = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));

$fileName = "{$timestamp}_{$name}.php";

$path = __DIR__ . "/migrations/$fileName";

$template = <<<PHP
<?php

class {$className}
{
    public function up()
    {
        //
    }

    public function down()
    {
        //
    }
}
PHP;

file_put_contents($path, $template);

echo "✅ Migración creada: $fileName\n";
