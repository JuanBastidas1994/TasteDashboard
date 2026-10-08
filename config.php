<?php
require_once __DIR__ . '/env.php';

$envFile = __DIR__ . '/.env';
if (!is_readable($envFile)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Falta el archivo .env. Copia .env.example a .env y configura las variables (Forge: Environment o SSH).";
    exit(1);
}

load_env($envFile);

define('servidor', env('DB_HOST', '127.0.0.1'));
define('db', env('DB_DATABASE', 'jc_taste'));
define('usuario', env('DB_USERNAME', 'root'));
define('contrasena', env('DB_PASSWORD', ''));

define('name_session', env('SESSION_NAME', 'ADMIN_MI_COMMERCE_WEB'));
define('DURACION_SESION', env('SESSION_LIFETIME', '7200'));

define('url_sistema', env('URL_SISTEMA', 'https://tastedashboard.test/'));
define('url_upload', rtrim(env('URL_UPLOAD', ''), '/') . '/');
define('url_pages_installers', env('URL_PAGES_INSTALLERS', ''));
define('url_folder_demo', env('URL_FOLDER_DEMO', ''));
define('firebaseMessagingToken', env('FIREBASE_MESSAGING_TOKEN', ''));
define('ENVIRONMENT', env('APP_ENV', 'development'));
define('url_laar', env('URL_LAAR', 'https://api.laarcourier.com:9727/'));

define('path_hosting', rtrim(env('PATH_HOSTING', '/home/forge'), '/'));

define('API_TASTE_URL', taste_api_base(env('API_TASTE_URL', 'https://tasteordenes.test'), '/gestion-ordenes'));
define('API_TASTE_ECOMMERCE', env('API_TASTE_ECOMMERCE', 'https://tasteapi.test'));
define('API_MOTORIZADOS_URL', env('API_MOTORIZADOS_URL', ''));
define('API_FLOTAS_URL', env('API_FLOTAS_URL', ''));
define('API_POS_URL', taste_api_base(env('API_POS_URL', 'https://tasteordenes.test'), '/pos/v3'));
// Mismo valor que TRACKING_SECRET de api/api_gestion_ordenes: el cron de notificaciones arma links de tracking
define('TRACKING_SECRET', env('TRACKING_SECRET', ''));
