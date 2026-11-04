<?php
/**
 * Webhook url_back de MedianetPay (WebCheckout).
 *
 * URL a configurar como url_back: {URL_TASTE_DASHBOARD}/webhooks/medianet_callback.php
 * (la arma el API front en controllers/Medianet.php con MEDIANET_CALLBACK_BASE).
 *
 * Medianet no envia cabeceras (Api-Key / User-Id), por eso este endpoint vive aqui y
 * no en el API front: resuelve la empresa desde la preorden (reference = "<cod_preorden>-<timestamp>"),
 * valida moneda y monto contra la preorden y crea la orden llamando al API front
 * con las cabeceras de la empresa (mismo flujo que nuvei_pago_complete.php).
 *
 * Respuestas: 200 = notificacion procesada (aprobada, rechazada o ya existia);
 * 500 solo cuando fallo crear la orden y conviene que se reintente.
 */

require_once "../funciones.php";
require_once "../clases/cl_empresas.php";
error_reporting(E_ALL);

const MEDIANET_PROVIDER = 4;
$fecha = fecha();
$API_BASE = rtrim(API_TASTE_ECOMMERCE, '/');

function medianetLog($carpetaEmpresa, $mensaje) {
    global $fecha;
    $carpeta = "medianet_pagos/" . ($carpetaEmpresa ? preg_replace('/[^A-Za-z0-9_-]/', '_', $carpetaEmpresa) : "_sin_identificar");
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0775, true);
    }
    file_put_contents($carpeta . "/" . date("Y-m-d") . ".log", PHP_EOL . $fecha . " - " . $mensaje, FILE_APPEND);
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
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 25,
    ]);
    $respuesta = curl_exec($ch);
    curl_close($ch);
    return json_decode($respuesta, true);
}

/** Monto cobrado con tarjeta segun la preorden (nunca el que manda el cliente). */
function montoTarjetaPreorden($json) {
    $trama = json_decode($json, true);
    $monto = 0;
    foreach (($trama['metodoPago'] ?? []) as $pago) {
        if (($pago['tipo'] ?? '') == "T") $monto += floatval($pago['monto']);
    }
    return round($monto, 2);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(200, ['success' => 0, 'mensaje' => 'Metodo no permitido, usar POST']);
}

$request = file_get_contents("php://input");
medianetLog(null, "REQUEST CRUDO (IP " . ($_SERVER['REMOTE_ADDR'] ?? '') . "): " . $request);

$input = json_decode($request, true);
if (JSON_ERROR_NONE !== json_last_error() || !is_array($input)) {
    medianetLog(null, "JSON invalido");
    responder(200, ['success' => 0, 'mensaje' => 'JSON invalido']);
}

// Lista de IPs de Medianet autorizadas (separadas por coma). Vacia = sin validacion (solo QA/local)
$ipsPermitidas = array_filter(array_map('trim', explode(',', env('MEDIANET_ALLOWED_IPS', ''))));
if (count($ipsPermitidas) > 0 && !in_array($_SERVER['REMOTE_ADDR'] ?? '', $ipsPermitidas, true)) {
    medianetLog(null, "IP no autorizada: " . ($_SERVER['REMOTE_ADDR'] ?? ''));
    responder(403, ['success' => 0, 'mensaje' => 'No autorizado']);
}

$reference = trim((string)($input['reference'] ?? ''));
$cod_preorden = intval(explode('-', $reference)[0]);
if ($cod_preorden <= 0) {
    medianetLog(null, "Referencia invalida: $reference");
    responder(200, ['success' => 0, 'mensaje' => 'Referencia invalida']);
}

$preorden = Conexion::buscarRegistro("SELECT * FROM tb_preorden_json WHERE cod_preorden = $cod_preorden");
if (!$preorden) {
    medianetLog(null, "Preorden $cod_preorden no existe");
    responder(200, ['success' => 0, 'mensaje' => 'Preorden no existe']);
}

$empresaDb = Conexion::buscarRegistro("SELECT e.cod_empresa, e.alias FROM tb_usuarios u
    JOIN tb_empresas e ON e.cod_empresa = u.cod_empresa
    WHERE u.cod_usuario = " . intval($preorden['cod_usuario']));
if (!$empresaDb) {
    medianetLog(null, "No se pudo resolver la empresa de la preorden $cod_preorden");
    responder(200, ['success' => 0, 'mensaje' => 'No se pudo resolver la empresa']);
}

$cod_empresa = $empresaDb['cod_empresa'];
$empresaLog = $empresaDb['alias'] ? $empresaDb['alias'] : $cod_empresa;
medianetLog($empresaLog, "REQUEST CRUDO: " . $request);

$ClEmpresas = new cl_empresas();
$empresa = $ClEmpresas->get($cod_empresa);
$apikey = $empresa ? $empresa['api_key'] : null;
$userId = $preorden['cod_usuario'];
if (!$apikey) {
    medianetLog($empresaLog, "Sin api_key para la empresa $cod_empresa");
    responder(500, ['success' => 0, 'mensaje' => 'Sin api_key de la empresa']);
}

$paymentId = substr((string)($input['payment_reference'] ?? ''), 0, 60);
$paymentAuth = substr((string)($input['authorization'] ?? ''), 0, 15);

// La orden ya existe (reintento del callback): idempotente
if (intval($preorden['cod_orden']) !== 0) {
    medianetLog($empresaLog, "Preorden $cod_preorden ya tiene orden #{$preorden['cod_orden']}");
    responder(200, ['success' => 1, 'mensaje' => 'Orden ya creada']);
}

$aprobada = mb_strtolower(trim((string)($input['response'] ?? ''))) === 'aprobada';
if (!$aprobada) {
    $resp = llamarApi("$API_BASE/ordenes/preorden-failure", $apikey, $userId, [
        'cod_preorden' => $cod_preorden,
        'paymentId' => $paymentId,
        'paymentAuth' => '',
        'motivo' => 'Pago rechazado Medianet',
    ]);
    medianetLog($empresaLog, "Preorden $cod_preorden pago no aprobado. Respuesta preorden-failure: " . json_encode($resp));
    responder(200, ['success' => 1, 'mensaje' => 'Pago no aprobado registrado']);
}

// Aprobado: monto y moneda deben coincidir con la preorden
$esperado = montoTarjetaPreorden($preorden['json']);
$recibido = round(floatval($input['amount'] ?? 0), 2);
if (($input['currency'] ?? '') !== 'USD' || abs($esperado - $recibido) > 0.01) {
    medianetLog($empresaLog, "CRITICO: monto/moneda no coinciden preorden=$cod_preorden esperado=$esperado recibido=$recibido moneda=" . ($input['currency'] ?? ''));
    responder(200, ['success' => 0, 'mensaje' => 'El monto no coincide con la preorden']);
}

$campos = [
    'cod_preorden' => $cod_preorden,
    'paymentId' => $paymentId,
    'paymentAuth' => $paymentAuth,
    'lot_number' => '',
];

$respPago = llamarApi("$API_BASE/ordenes/preorden-pago-exitoso", $apikey, $userId, $campos);
medianetLog($empresaLog, "Respuesta preorden-pago-exitoso: " . json_encode($respPago));

$campos['paymentProvider'] = MEDIANET_PROVIDER;
$respOrden = llamarApi("$API_BASE/ordenes/preorden", $apikey, $userId, $campos);
medianetLog($empresaLog, "Respuesta preorden: " . json_encode($respOrden));

if ($respOrden && isset($respOrden['success']) && $respOrden['success'] == 1) {
    responder(200, [
        'success' => 1,
        'mensaje' => 'Orden creada correctamente',
        'orderID' => isset($respOrden['id']) ? $respOrden['id'] : null,
    ]);
}

medianetLog($empresaLog, "Fallo al crear la orden via API (preorden $cod_preorden)");
responder(500, ['success' => 0, 'mensaje' => 'Error creando la orden']);
