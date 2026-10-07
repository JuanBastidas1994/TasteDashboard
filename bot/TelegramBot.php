<?php
/**
 * Bot de Telegram de Taste: vincula el chat de un administrador con su usuario del dashboard.
 *
 * Flujo (mismo que el bot anterior):
 *   1. El usuario entra a Perfil del dashboard y se crea una fila pendiente en tb_telegram_usuarios (estado 'P') con un código.
 *   2. Le escribe ese código al bot (o abre el enlace "Hablar con el bot", que envía /start <código>).
 *   3. Aquí se valida el código, se guardan chat_id/user_id y la fila pasa a 'A' (activo).
 *   4. Desde ese momento api y api_gestion_ordenes le envían los avisos (NOTIFY_TELEGRAM): orden nueva / orden asignada a la flota.
 *
 * Config (.env del dashboard):
 *   TELEGRAM_BOT_TOKEN       token del bot (BotFather)
 *   TELEGRAM_WEBHOOK_SECRET  secreto que Telegram envía en cada petición (se fija con bot/set_webhook.php)
 *   TELEGRAM_API_URL         opcional, solo para pruebas con un servidor falso
 */
class TelegramBot
{
    const API_URL = 'https://api.telegram.org/bot';
    // Solo Administrador Empresa (2) y Administrador Sucursal (3) pueden vincularse
    const ROLES_PERMITIDOS = [2, 3];

    private $token;
    private $apiUrl;

    public function __construct()
    {
        $this->token = trim((string) $this->env('TELEGRAM_BOT_TOKEN'));
        $this->apiUrl = rtrim($this->env('TELEGRAM_API_URL', self::API_URL), '/');
    }

    // Telegram envía el secreto fijado en setWebhook en este encabezado. Sin secreto configurado se rechaza todo.
    public function secretoValido($recibido)
    {
        $esperado = trim((string) $this->env('TELEGRAM_WEBHOOK_SECRET'));
        return $esperado !== '' && is_string($recibido) && hash_equals($esperado, $recibido);
    }

    /**
     * Procesa una actualización de Telegram. Retorna un texto corto del resultado, para el log
     * (nunca incluye el contenido del mensaje ni el código).
     */
    public function procesar(array $update)
    {
        if (!isset($update['message']) || !isset($update['message']['chat']['id'])) {
            return 'ignorado: no es un mensaje';
        }
        $mensaje = $update['message'];
        $chat = $mensaje['chat'];
        if (($chat['type'] ?? 'private') !== 'private') {
            return 'ignorado: no es chat privado';
        }
        if (!isset($mensaje['text'])) {
            return 'ignorado: sin texto';
        }

        $chat_id = (string) $chat['id'];
        $person_id = (string) ($mensaje['from']['id'] ?? '');
        $texto = trim($mensaje['text']);

        // Chat ya vinculado
        if ($this->chatVerificado($chat_id)) {
            $this->sendMessage($chat_id, "Aún no puedo analizar tus mensajes, por ahora solo puedo enviarte las ordenes que ingresen a tu sistema.");
            return 'chat ya vinculado';
        }

        // "/start" solo, o "/start <código>" (enlace "Hablar con el bot" del Perfil)
        $codigo = $texto;
        if (preg_match('/^\/start(?:@\w+)?(?:\s+(.*))?$/i', $texto, $m)) {
            $codigo = isset($m[1]) ? trim($m[1]) : '';
            if ($codigo === '') {
                $this->sendMessage($chat_id, "Bienvenido al asesor de ordenes de Taste, debes ingresar el código generado en tu Perfil del dashboard (sección Telegram).");
                return 'start sin código';
            }
        }
        $codigo = strtoupper($codigo);

        // Código con formato imposible: no se consulta la BD
        if (!preg_match('/^[A-Z0-9]{4,15}$/', $codigo)) {
            $this->sendMessage($chat_id, "El codigo ingresado es incorrecto, por favor ingresa el código proporcionado en tu Perfil del dashboard (sección Telegram).");
            return 'código con formato inválido';
        }

        $usuario = $this->validarCodigoAsignacion($codigo);
        if (!$usuario) {
            $this->sendMessage($chat_id, "El codigo ingresado es incorrecto, por favor ingresa el código proporcionado en tu Perfil del dashboard (sección Telegram).");
            return 'código incorrecto';
        }
        if (!in_array((int) $usuario['cod_rol'], self::ROLES_PERMITIDOS, true)) {
            $this->sendMessage($chat_id, "No tienes permisos para usar el bot, consulta con el administrador del comercio");
            return 'rol sin permiso';
        }
        if ($this->asignarCodigo($codigo, $chat_id, $person_id)) {
            $nombre = htmlspecialchars((string) $usuario['nombre'], ENT_NOQUOTES, 'UTF-8');
            $this->sendMessage($chat_id, "Bienvenido $nombre, ya quedaste vinculado: recibirás aquí las ordenes de tu sistema.");
            return 'vinculado';
        }
        $this->sendMessage($chat_id, "Ocurrió un error, intentalo nuevamente");
        return 'error al vincular';
    }

    /* ---------------- Base de datos (todas con consultas preparadas) ---------------- */

    public function chatVerificado($chat_id)
    {
        return $this->uno("SELECT id FROM tb_telegram_usuarios WHERE chat_id = :chat_id AND estado = 'A' LIMIT 1", [':chat_id' => $chat_id]);
    }

    public function validarCodigoAsignacion($codigo)
    {
        return $this->uno(
            "SELECT tu.id, tu.cod_usuario, u.nombre, u.cod_rol
             FROM tb_telegram_usuarios tu
             INNER JOIN tb_usuarios u ON u.cod_usuario = tu.cod_usuario AND u.estado = 'A'
             WHERE tu.code = :code AND tu.estado = 'P'
             LIMIT 1",
            [':code' => $codigo]
        );
    }

    // true solo si realmente se actualizó una fila pendiente (dos chats no pueden usar el mismo código)
    public function asignarCodigo($codigo, $chat_id, $person_id)
    {
        $st = Conexion::obtenerConexion()->prepare(
            "UPDATE tb_telegram_usuarios SET chat_id = :chat_id, user_id = :user_id, estado = 'A'
             WHERE code = :code AND estado = 'P'"
        );
        $st->execute([':chat_id' => $chat_id, ':user_id' => $person_id, ':code' => $codigo]);
        return $st->rowCount() === 1;
    }

    private function uno($sql, array $params)
    {
        $st = Conexion::obtenerConexion()->prepare($sql);
        $st->execute($params);
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    /* ---------------- Telegram ---------------- */

    public function sendMessage($chat_id, $texto)
    {
        return $this->llamar('sendMessage', ['chat_id' => $chat_id, 'text' => $texto, 'parse_mode' => 'HTML']);
    }

    // Llama a un método de la API de Telegram. Retorna el JSON decodificado o null si no hubo respuesta.
    public function llamar($metodo, array $params = [])
    {
        if ($this->token === '') {
            return null;
        }
        $ch = curl_init($this->apiUrl . $this->token . '/' . $metodo);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 8,
        ]);
        $respuesta = curl_exec($ch);
        curl_close($ch);
        return $respuesta === false ? null : json_decode($respuesta, true);
    }

    private function env($clave, $defecto = '')
    {
        $valor = function_exists('env') ? env($clave, $defecto) : $defecto;
        return $valor === null ? $defecto : (string) $valor;
    }
}
