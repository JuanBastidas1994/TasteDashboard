<?php
require_once __DIR__ . '/env.php';
load_env(__DIR__ . '/.env');

define('servidor', env('DB_HOST', '127.0.0.1'));
define('db', env('DB_DATABASE', 'jc_taste'));
define('usuario', env('DB_USERNAME', 'root'));
define('contrasena', env('DB_PASSWORD', ''));

define('name_session', env('SESSION_NAME', 'ADMIN_MI_COMMERCE_WEB'));
define('DURACION_SESION', env('SESSION_LIFETIME', '7200'));

define('url_sistema', env('URL_SISTEMA', 'https://tastedashboard.test/'));
define('url_upload', env('URL_UPLOAD', ''));
define('url_pages_installers', env('URL_PAGES_INSTALLERS', ''));
define('url_bot', env('URL_BOT', 'https://tastedashboard.test/bot/'));
define('url_folder_demo', env('URL_FOLDER_DEMO', ''));
define('firebaseMessagingToken', env('FIREBASE_MESSAGING_TOKEN', ''));
define('ENVIRONMENT', env('APP_ENV', 'development'));
define('url_laar', env('URL_LAAR', 'https://api.laarcourier.com:9727/'));

define('API_TASTE_URL', env('API_TASTE_URL', 'https://tasteordenes.test'));
define('API_TASTE_ECOMMERCE', env('API_TASTE_ECOMMERCE', 'https://tasteapi.test'));
define('API_MOTORIZADOS_URL', env('API_MOTORIZADOS_URL', ''));
define('API_FLOTAS_URL', env('API_FLOTAS_URL', ''));
define('API_POS_URL', env('API_POS_URL', 'https://tasteordenes.test'));
