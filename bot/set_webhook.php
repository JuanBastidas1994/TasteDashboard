<?php
/**
 * Administra el webhook del bot desde la terminal (usa el .env, nunca muestra el token):
 *
 *   php bot/set_webhook.php me                       datos del bot (usuario para TELEGRAM_BOT_USERNAME)
 *   php bot/set_webhook.php info                     estado actual del webhook
 *   php bot/set_webhook.php set https://dominio/bot/ registra el webhook con TELEGRAM_WEBHOOK_SECRET
 *   php bot/set_webhook.php delete                   quita el webhook
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/TelegramBot.php';

$bot = new TelegramBot();
$accion = $argv[1] ?? '';

switch ($accion) {
    case 'me':
        $r = $bot->llamar('getMe');
        break;
    case 'info':
        $r = $bot->llamar('getWebhookInfo');
        break;
    case 'delete':
        $r = $bot->llamar('deleteWebhook');
        break;
    case 'set':
        $url = $argv[2] ?? '';
        $secreto = trim((string) env('TELEGRAM_WEBHOOK_SECRET', ''));
        if (!preg_match('#^https://#i', $url)) {
            fwrite(STDERR, "La URL debe ser https (Telegram no acepta http).\n");
            exit(1);
        }
        if ($secreto === '' || !preg_match('/^[A-Za-z0-9_-]{16,256}$/', $secreto)) {
            fwrite(STDERR, "Define TELEGRAM_WEBHOOK_SECRET en el .env (16 a 256 caracteres: letras, números, _ y -).\n");
            exit(1);
        }
        $r = $bot->llamar('setWebhook', [
            'url' => $url,
            'secret_token' => $secreto,
            'allowed_updates' => json_encode(['message']),
        ]);
        break;
    default:
        fwrite(STDERR, "Uso: php bot/set_webhook.php me | info | set <https://dominio/bot/> | delete\n");
        exit(1);
}

if ($r === null) {
    fwrite(STDERR, "Sin respuesta de Telegram: revisa TELEGRAM_BOT_TOKEN y la conexión.\n");
    exit(1);
}
echo json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "\n";
exit(empty($r['ok']) ? 1 : 0);
