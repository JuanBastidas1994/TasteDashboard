<?php
/* Utilidades de /api/riders/v1: respuestas, validación, geografía y helpers de URL. */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../../conexion.php';

/**
 * Error de negocio: sale como HTTP 200 con `success` 0 y el texto en `mensaje` (ya listo para mostrar en la app).
 * `success` -1 es "sesión no válida": la app cierra sesión.
 */
class RidersException extends Exception {
    public $success;

    public function __construct($mensaje, $success = 0) {
        parent::__construct($mensaje);
        $this->success = $success;
    }
}

function fecha() {
    return date('Y-m-d H:i:s');
}

function jsonResponse($data, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);
    exit();
}

function responderOk($body = [], $mensaje = 'Correcto') {
    jsonResponse(array_merge(['success' => 1, 'mensaje' => $mensaje], $body));
}

function responderError($mensaje, $success = 0) {
    jsonResponse(['success' => $success, 'mensaje' => $mensaje]);
}

/** Sin sesión válida: único caso con HTTP 401 (la app cierra sesión). */
function responderNoAutorizado($mensaje = 'No autorizado') {
    jsonResponse(['success' => -1, 'mensaje' => $mensaje], 401);
}

function logError($e) {
    $archivo = __DIR__ . '/../../logs/riders_api.log';
    @file_put_contents($archivo, '[' . fecha() . '] ' . get_class($e) . ': ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL, FILE_APPEND);
}

function getHeader($name) {
    if (function_exists('getallheaders')) {
        foreach (getallheaders() as $key => $value) {
            if (strcasecmp($key, $name) === 0) return $value;
        }
    }
    $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
    return $_SERVER[$serverKey] ?? ($_SERVER['REDIRECT_' . $serverKey] ?? null);
}

/** Token de la cabecera `Authorization: Bearer ...`. */
function bearerToken() {
    $h = getHeader('Authorization');
    return ($h && stripos($h, 'Bearer ') === 0) ? trim(substr($h, 7)) : null;
}

/**
 * Valida el JSON/query contra reglas "campo => 'required|int|num:-90,90|str:255|bool'" y devuelve solo los campos declarados.
 * Los mensajes son los del dashboard nuevo (ya listos para mostrar en la app).
 */
function validar(array $input, array $reglas) {
    $salida = [];
    foreach ($reglas as $campo => $regla) {
        $partes = explode('|', $regla);
        $nombre = str_replace('_', ' ', strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $campo)));
        $valor = $input[$campo] ?? null;
        if ($valor === '') $valor = null;

        if ($valor === null || $valor === []) {
            if (in_array('required', $partes, true)) throw new RidersException("Falta información: el campo $nombre es obligatorio");
            continue;
        }

        foreach ($partes as $parte) {
            $args = explode(':', $parte, 2);
            $tipo = $args[0];
            $invalido = new RidersException("El valor de $nombre no es válido");
            if ($tipo === 'int') {
                if (!(is_int($valor) || (is_string($valor) && preg_match('/^-?\d+$/', $valor)))) throw $invalido;
                $valor = (int) $valor;
            } elseif ($tipo === 'num') {
                if (is_bool($valor) || !is_numeric($valor)) throw $invalido;
                $valor = (float) $valor;
                if (isset($args[1])) {
                    list($min, $max) = array_map('floatval', explode(',', $args[1]));
                    if ($valor < $min || $valor > $max) throw $invalido;
                }
            } elseif ($tipo === 'str') {
                if (!is_string($valor)) throw $invalido;
                if (isset($args[1]) && mb_strlen($valor) > (int) $args[1]) throw new RidersException("El campo $nombre es muy largo");
            } elseif ($tipo === 'bool') {
                if (!in_array($valor, [true, false, 1, 0, '1', '0'], true)) throw $invalido;
                $valor = (bool) $valor;
            }
        }
        $salida[$campo] = $valor;
    }
    return $salida;
}

/** Coordenada válida o null (las columnas legacy son varchar y a veces vienen vacías). */
function coordenada($valor) {
    return ($valor !== null && $valor !== '' && is_numeric($valor)) ? (float) $valor : null;
}

/** Distancia en línea recta entre dos coordenadas (metros), fórmula de Haversine. */
function distanciaMetros($lat1, $lon1, $lat2, $lon2) {
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
    return 6371000 * 2 * atan2(sqrt($a), sqrt(1 - $a));
}

/** "850 m" o "2,4 km". */
function formatoDistancia($metros) {
    return $metros < 1000 ? "$metros m" : number_format($metros / 1000, 1, ',', '') . ' km';
}

/** URL pública de un archivo subido para una empresa (logo, foto de motorizado). Una URL completa se deja igual. */
function urlAssetEmpresa($alias, $archivo) {
    if (!$archivo) return null;
    if (preg_match('#^https?://#i', $archivo)) return $archivo;
    if (!$alias) return null;
    return rtrim(url_sistema, '/') . '/assets/empresas/' . $alias . '/' . $archivo;
}

/** Fecha de MySQL para la app, o null si viene vacía / en cero. */
function fechaApp($valor) {
    return ($valor && strpos($valor, '0000-00-00') !== 0) ? $valor : null;
}

/** Mismo formato que api/funciones.php: el link "Ver pedido" del push abre /tracking/v2/{token} de la tienda. */
function generarTracking($cod_orden) {
    $sig = substr(hash_hmac('sha256', (string) $cod_orden, TRACKING_SECRET), 0, 12);
    return rtrim(base64_encode($cod_orden . '.' . $sig), '=');
}
