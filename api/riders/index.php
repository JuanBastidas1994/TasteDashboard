<?php
/*
 * /api/riders/v1: la API de la app de motorizados (flotas y comercios con motorizados propios).
 *
 * Es la misma API que expone el dashboard nuevo (Laravel, routes/riders.php): mismas rutas, llaves y mensajes, para que la app
 * funcione igual contra cualquiera de los dos y solo cambie su apiUrl. Cualquier cambio de contrato se hace en los dos.
 *
 * Sobre de respuesta (el de api_flotas): `success` 1 bien, 0 error de negocio, -1 sin sesión; `mensaje`; y el resto de llaves.
 * Los errores de negocio salen como HTTP 200; el único 401 es "sin sesión válida" (la app cierra sesión).
 * Sesión: `Authorization: Bearer <token>` (lo entrega el login). Ya no se manda `Api-Key` ni `User-Id`.
 */
ini_set('display_errors', 0);
error_reporting(0);

require_once __DIR__ . '/funciones.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Device-Type, Content-Type, *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'OPTIONS') {
    responderOk([], 'Validación completa');
}

$rutas = [
    'usuarios' => 'controllers/Usuarios.php',
    'ordenes' => 'controllers/Ordenes.php',
    'notificaciones' => 'controllers/Notificaciones.php',
    'configuracion' => 'controllers/Configuracion.php',
];

// Rutas sin sesión: el login, canjear el código con Google, y la configuración (la app la pide antes de iniciar sesión)
$publicas = ['POST usuarios/login', 'POST usuarios/login_google', 'POST usuarios/canjear-codigo', 'GET configuracion'];

try {
    // /api/riders/v1/usuarios/login  o  /api/riders/index.php/usuarios/login
    $ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $ruta = preg_match('#/api/riders/(?:v1/|index\.php/)?(.*)$#', $ruta, $m) ? trim($m[1], '/') : '';
    $request = $ruta === '' ? [] : explode('/', $ruta);
    $recurso = $request[0] ?? '';

    $motorizado = null;
    if (!in_array($method . ' ' . $ruta, $publicas, true)) {
        require_once __DIR__ . '/clases/cl_riders_sesion.php';
        $token = bearerToken();
        $motorizado = $token ? (new cl_riders_sesion())->autenticar($token) : null;
        if (!$motorizado) responderNoAutorizado();
    }

    $input = [];
    if ($method === 'POST' || $method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($input)) {
            responderError('El JSON de entrada no tiene un formato correcto');
        }
    }

    if (isset($rutas[$recurso])) {
        require __DIR__ . '/' . $rutas[$recurso];
    }

    // Cualquier otra ruta o método
    responderError('Evento no existente, por favor verificar la URL.');
} catch (RidersException $e) {
    responderError($e->getMessage(), $e->success);
} catch (Throwable $e) {
    logError($e);
    responderError('Ocurrió un error');
}
