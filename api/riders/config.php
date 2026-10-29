<?php
/*
 * Configuración de /api/riders/v1 (la API de la app de motorizados). Reusa la conexión y el .env del dashboard;
 * los nombres RIDERS_* son los mismos que usa el dashboard nuevo (config/riders.php) para que ambos se configuren igual.
 */
require_once __DIR__ . '/../../config.php';

// PHP corre en UTC y MySQL en hora de Ecuador: sin esto las fechas quedan 5 h adelantadas
date_default_timezone_set('America/Guayaquil');

define('ROL_MOTORIZADO', (int) env('ROL_MOTORIZADO', 17));

// Geofence automático (metros, línea recta): "llegó al local" y "cerca del cliente"
define('RIDERS_GEOFENCE_STORE_METERS', (int) env('RIDERS_GEOFENCE_STORE_METERS', 100));
define('RIDERS_GEOFENCE_CUSTOMER_METERS', (int) env('RIDERS_GEOFENCE_CUSTOMER_METERS', 600));
// Marcar a mano "llegué" / "entregado" más lejos que esto pide confirmación (no se bloquea: el GPS puede fallar)
define('RIDERS_FAR_STORE_METERS', (int) env('RIDERS_FAR_STORE_METERS', 300));
define('RIDERS_FAR_CUSTOMER_METERS', (int) env('RIDERS_FAR_CUSTOMER_METERS', 500));
// Una ubicación guardada más vieja que esto (minutos) no sirve para decidir que ya llegó
define('RIDERS_LOCATION_FRESH_MINUTES', (int) env('RIDERS_LOCATION_FRESH_MINUTES', 10));

// Contacto de Taste que muestra la app (soporte)
define('RIDERS_APP_PHONE', env('RIDERS_APP_PHONE', ''));
define('RIDERS_APP_EMAIL', env('RIDERS_APP_EMAIL', ''));
define('RIDERS_APP_ADDRESS', env('RIDERS_APP_ADDRESS', ''));
// Aviso de versión: sale de tb_empresas_versiones_app (cod_empresa de Taste); la url cae al env si la fila no la trae
define('RIDERS_APP_COD_EMPRESA', (int) env('RIDERS_APP_COD_EMPRESA', 1));
define('RIDERS_APP_ANDROID_URL', env('RIDERS_APP_ANDROID_URL', ''));

// ID de cliente web de Google con el que la app firma el id_token (webClientId de brand.json); separar varios con coma
define('RIDERS_GOOGLE_CLIENT_IDS', env('RIDERS_GOOGLE_CLIENT_IDS', '407406159229-1rbmeedhilsdfvmufln0p76or04hnc7k.apps.googleusercontent.com'));

define('EXPO_PUSH_URL', env('EXPO_PUSH_URL', 'https://exp.host/--/api/v2/push/send'));
// Proyecto de Firebase donde Gestión de Órdenes escucha los cambios de estado
define('RIDERS_FIREBASE_PROJECT', env('RIDERS_FIREBASE_PROJECT', 'ptoventa-3b5ed'));

// Intentos de login permitidos por ventana (segundos)
define('RIDERS_LOGIN_MAX_INTENTOS', 8);
define('RIDERS_LOGIN_VENTANA', 300);
