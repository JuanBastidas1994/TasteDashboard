<?php
/**
 * Webhook del bot de Telegram (https://<dashboard>/bot/).
 * Registrarlo con:  php bot/set_webhook.php set https://<dashboard>/bot/
 *
 * - No usa funciones.php (sin sesión ni cookies): solo config.php (.env) y conexion.php.
 * - Rechaza toda petición que no traiga el secreto del webhook (TELEGRAM_WEBHOOK_SECRET).
 * - Siempre responde 200 a Telegram una vez autenticada la petición: si responde error, Telegram reintenta sin parar.
 * - El log (logs/bot-telegram.log) guarda solo fecha, tipo de resultado y update_id: nunca textos, códigos ni nombres.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/TelegramBot.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

function botLog($mensaje)
{
    $carpeta = __DIR__ . '/../logs';
    if (!is_dir($carpeta)) {
        @mkdir($carpeta, 0775, true);
    }
    @file_put_contents($carpeta . '/bot-telegram.log', '[' . date('Y-m-d H:i:s') . '] ' . $mensaje . "\n", FILE_APPEND | LOCK_EX);
}

function botRespuesta($codigo, array $cuerpo)
{
    http_response_code($codigo);
    echo json_encode($cuerpo);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    botRespuesta(405, ['ok' => false]);
}

$bot = new TelegramBot();
$secreto = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '';
if (!$bot->secretoValido($secreto)) {
    botLog('rechazado: secreto inválido o TELEGRAM_WEBHOOK_SECRET sin configurar');
    botRespuesta(403, ['ok' => false]);
}

$update = json_decode(file_get_contents('php://input'), true);
if (!is_array($update)) {
    botLog('ignorado: cuerpo que no es JSON');
    botRespuesta(200, ['ok' => true]);
}

try {
    $resultado = $bot->procesar($update);
    botLog('update ' . ($update['update_id'] ?? '?') . ': ' . $resultado);
} catch (Throwable $e) {
    botLog('update ' . ($update['update_id'] ?? '?') . ': ERROR ' . get_class($e) . ' ' . $e->getMessage() . ' en ' . basename($e->getFile()) . ':' . $e->getLine());
}
botRespuesta(200, ['ok' => true]);
