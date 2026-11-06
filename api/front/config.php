<?php
/*
 * Configuración de /api/front/v1 (la API de la web y app de clientes, antes api.mie-commerce.com/front).
 * Reusa el .env y env.php del dashboard (raíz del proyecto). Las llaves nuevas están documentadas al final de .env.example.
 */
require_once __DIR__ . '/../../env.php';

$envFile = __DIR__ . '/../../.env';
if (!is_readable($envFile)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => 0, 'mensaje' => 'Falta el archivo .env del dashboard']);
    exit(1);
}
load_env($envFile);

define('servidor', env('DB_HOST', '127.0.0.1'));
define('db', env('DB_DATABASE', 'jc_taste'));
define('usuario', env('DB_USERNAME', 'root'));
define('contrasena', env('DB_PASSWORD', ''));

define('ENVIRONMENT', env('APP_ENV', 'development'));
define('url_sistema', rtrim((string) env('URL_SISTEMA', 'https://tastedashboard.test/'), '/') . '/');
define('url_upload', env('URL_UPLOAD', ''));
// Imágenes fijas del API (niveles de fidelización, pasos del tracking). Por defecto cuelgan del dashboard.
define('url_resource', env('URL_RESOURCE', url_sistema . 'assets/resource/api/'));

// Versión que se guarda en tb_orden_cabecera.api_version cuando el checkout no manda una
define('api_version', env('API_VERSION', 'v2'));
define('LOGIN_EMAIL_NUM_DIGITS', (int) env('LOGIN_EMAIL_NUM_DIGITS', 4));

define('TRACKING_SECRET', env('TRACKING_SECRET', ''));

// MedianetPay: URL COMPLETA del webhook url_back (se envía tal cual a Medianet, sin concatenar nada)
define('MEDIANET_URL_BACK', env('MEDIANET_URL_BACK', ''));
define('MEDIANET_ALLOWED_IPS', env('MEDIANET_ALLOWED_IPS', ''));

if (!defined('API_GOOGLE_MAPS')) {
    define('API_GOOGLE_MAPS', env('API_GOOGLE_MAPS', ''));
}
