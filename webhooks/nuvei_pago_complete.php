<?php
/**
 * Webhook de confirmacion de pago Nuvei (ex Paymentez).
 *
 * Este endpoint NO es el camino principal para crear la orden -- eso lo hace
 * la web/app inmediatamente despues del pago (ver ProcessingPaymentPage en
 * taste-web). Este webhook es solo un blindaje: si el usuario cierra la
 * web/app antes de que se alcance a crear la orden, Nuvei sigue reintentando
 * este aviso automaticamente (segun su doc: primer reintento a los ~5 min,
 * luego cada ~10 min, hasta 48h si nunca respondemos 200), y en el segundo
 * aviso (o posterior) creamos la orden nosotros si todavia no existe.
 *
 * A proposito NO se crea la orden en el primer aviso: llega casi
 * instantaneo al pago, antes de que la web/app tenga chance de responder.
 * Se deja pasar el primer reintento de Nuvei (~5 min) como margen para que
 * el camino normal (web/app) termine primero.
 */

require_once "../funciones.php";
require_once "../clases/cl_empresas.php";
require_once "../clases/cl_botonPagos.php";
error_reporting(E_ALL);

$fecha = fecha();
$API_BASE = API_TASTE_ECOMMERCE;

function nuveiLog($cod_empresa, $mensaje) {
    global $fecha;
    $carpeta = "nuvei_pagos/" . ($cod_empresa ? $cod_empresa : "_sin_identificar");
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0775, true);
    }
    $archivo = $carpeta . "/" . date("Y-m-d") . ".log";
    file_put_contents($archivo, PHP_EOL . $fecha . " - " . $mensaje, FILE_APPEND);
}

function responder($http_code, $payload) {
    http_response_code($http_code);
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($payload);
    exit();
}

function llamarApi($url, $apikey, $userId, $campos) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($campos),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Api-Key: " . $apikey,
            "User-Id: " . $userId,
            "Device-Type: WEBHOOK",
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    $respuesta = curl_exec($ch);
    curl_close($ch);
    return json_decode($respuesta, true);
}

$request = file_get_contents("php://input");
nuveiLog(null, "REQUEST CRUDO: " . $request);

$body = json_decode($request);
if (JSON_ERROR_NONE !== json_last_error() || !isset($body->transaction)) {
    nuveiLog(null, "JSON invalido o sin 'transaction'");
    responder(200, ['success' => 0, 'mensaje' => 'JSON invalido o sin transaction']);
}

$t = $body->transaction;

if (!isset($t->dev_reference) || !isset($t->id) || !isset($t->stoken) || !isset($t->application_code)) {
    nuveiLog(null, "Faltan campos obligatorios en la transaccion");
    responder(200, ['success' => 0, 'mensaje' => 'Faltan campos obligatorios']);
}

$cod_preorden = intval($t->dev_reference);

$preorden = Conexion::buscarRegistro("SELECT * FROM tb_preorden_json WHERE cod_preorden = $cod_preorden");
if (!$preorden) {
    nuveiLog(null, "Preorden $cod_preorden no existe");
    responder(200, ['success' => 0, 'mensaje' => 'Preorden no existe']);
}

$preordenJson = json_decode($preorden['json']);
$cod_sucursal = isset($preordenJson->cod_sucursal) ? $preordenJson->cod_sucursal : null;
if (!$cod_sucursal) {
    nuveiLog(null, "Preorden $cod_preorden sin cod_sucursal en su json");
    responder(200, ['success' => 0, 'mensaje' => 'Preorden sin sucursal']);
}

$sucursal = Conexion::buscarRegistro("SELECT cod_empresa FROM tb_sucursales WHERE cod_sucursal = $cod_sucursal");
$cod_empresa = $sucursal ? $sucursal['cod_empresa'] : null;
if (!$cod_empresa) {
    nuveiLog(null, "No se pudo resolver cod_empresa para sucursal $cod_sucursal (preorden $cod_preorden)");
    responder(200, ['success' => 0, 'mensaje' => 'No se pudo resolver la empresa']);
}

$ClBotonPagos = new cl_botonpagos();
$credsSucursal = $ClBotonPagos->sucursalPaymentez($cod_sucursal);
$server_key = $credsSucursal ? $credsSucursal['server_key'] : null;

if (!$server_key) {
    $credsEmpresa = $ClBotonPagos->datos_paymentez($cod_empresa);
    $server_key = $credsEmpresa ? $credsEmpresa['server_key'] : null;
}

if (!$server_key) {
    nuveiLog($cod_empresa, "No hay credenciales Nuvei/Paymentez para sucursal $cod_sucursal / empresa $cod_empresa");
    responder(200, ['success' => 0, 'mensaje' => 'Sin credenciales Nuvei configuradas']);
}

$stokenEsperado = md5("{$t->id}_{$t->application_code}_{$preorden['cod_usuario']}_{$server_key}");
if ($t->stoken !== $stokenEsperado) {
    nuveiLog($cod_empresa, "stoken invalido para preorden $cod_preorden (posible intento fraudulento)");
    responder(200, ['success' => 0, 'mensaje' => 'stoken invalido']);
}

$status = isset($t->status) ? intval($t->status) : null;
$statusDetail = isset($t->status_detail) ? intval($t->status_detail) : null;
if ($status !== 1 || $statusDetail !== 3) {
    nuveiLog($cod_empresa, "Preorden $cod_preorden: transaccion no aprobada (status=$status, status_detail=$statusDetail), sin accion");
    responder(200, ['success' => 1, 'mensaje' => 'Transaccion no aprobada, sin accion']);
}

if (intval($preorden['cod_orden']) !== 0) {
    nuveiLog($cod_empresa, "Preorden $cod_preorden ya tiene orden #{$preorden['cod_orden']}, nada que hacer");
    responder(200, ['success' => 1, 'mensaje' => 'La orden ya existia']);
}

Conexion::ejecutar("UPDATE tb_preorden_json SET webhook = IFNULL(webhook, 0) + 1 WHERE cod_preorden = $cod_preorden", null);
$avisoActual = Conexion::buscarRegistro("SELECT webhook FROM tb_preorden_json WHERE cod_preorden = $cod_preorden");
$intentos = $avisoActual ? intval($avisoActual['webhook']) : 1;

if ($intentos < 2) {
    nuveiLog($cod_empresa, "Aviso #$intentos para preorden $cod_preorden: esperando a que la web/app la cree primero");
    responder(404, ['success' => 1, 'mensaje' => 'Registrado, en espera de confirmacion de la web/app']);
}

nuveiLog($cod_empresa, "Aviso #$intentos para preorden $cod_preorden: la web/app no la creo, creando via API como blindaje");

$ClEmpresas = new cl_empresas();
$empresa = $ClEmpresas->get($cod_empresa);
$apikey = $empresa ? $empresa['api_key'] : null;
$userId = $preorden['cod_usuario'];

if (!$apikey) {
    nuveiLog($cod_empresa, "No se encontro api_key para la empresa $cod_empresa, no se puede llamar al API");
    responder(404, ['success' => 0, 'mensaje' => 'Sin api_key de la empresa, se reintentara']);
}

$campos = [
    'cod_preorden' => $cod_preorden,
    'paymentId' => $t->id,
    'paymentAuth' => isset($t->authorization_code) ? $t->authorization_code : '',
    'lot_number' => isset($t->lot_number) ? $t->lot_number : '',
];

$respPago = llamarApi("$API_BASE/ordenes/preorden-pago-exitoso", $apikey, $userId, $campos);
nuveiLog($cod_empresa, "Respuesta preorden-pago-exitoso: " . json_encode($respPago));

$campos['paymentProvider'] = 2;
$respOrden = llamarApi("$API_BASE/ordenes/preorden", $apikey, $userId, $campos);
nuveiLog($cod_empresa, "Respuesta preorden: " . json_encode($respOrden));

if ($respOrden && isset($respOrden['success']) && $respOrden['success'] == 1) {
    responder(200, [
        'success' => 1,
        'mensaje' => isset($respOrden['mensaje']) ? $respOrden['mensaje'] : 'Orden creada',
        'orderID' => isset($respOrden['id']) ? $respOrden['id'] : null,
    ]);
}

nuveiLog($cod_empresa, "Fallo al crear la orden via API, se deja que Nuvei siga reintentando");
responder(404, ['success' => 0, 'mensaje' => 'Error creando la orden, se reintentara']);
