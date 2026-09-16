<?php
require_once dirname(__DIR__) . '/env.php';
load_env(dirname(__DIR__) . '/.env');

define('servidor', env('DB_HOST', '127.0.0.1'));
define('db', env('DB_DATABASE', 'jc_taste'));
define('usuario', env('DB_USERNAME', 'root'));
define('contrasena', env('DB_PASSWORD', ''));

define('name_session', env('RUNFOOD_SESSION_NAME', 'ADMIN_FEED_CREW'));
define('DURACION_SESION', env('SESSION_LIFETIME', '7200'));
define('url_sistema', env('URL_SISTEMA', 'https://tastedashboard.test/'));
define('url_upload', rtrim(env('URL_UPLOAD', ''), '/') . '/');
define('ENVIRONMENT', env('APP_ENV', 'development'));
