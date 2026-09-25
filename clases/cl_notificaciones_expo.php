<?php
/* ==========================================================================
   NOTIFICACIONES PUSH (Expo) DESDE EL DASHBOARD
   - Masivas a clientes: promos, productos nuevos y eventos (notificaciones.php).
   - Mensaje libre a UN usuario (campana en clientes.php, cliente_detalle.php, usuario_detalle.php).
   Lee tb_push_tokens (la llenan las apps vía api_gestion_ordenes/api/api_flotas), no se llama
   ningún endpoint de esos proyectos: misma base de datos (jc_taste).
   ========================================================================== */

class cl_notificaciones_expo
{
    public $session;
    public $cod_empresa;

    /* Roles que usan la app de motorizados (Delivery y Motorizado) */
    private $ROLES_MOTORIZADO = [17, 21];

    public function __construct()
    {
        $this->session = getSession();
        $this->cod_empresa = $this->session['cod_empresa'];
    }

    /* Historial de la empresa con el nombre del admin que envió */
    public function lista($limite = 100)
    {
        $query = "SELECT n.*, CONCAT(u.nombre, ' ', u.apellido) as admin
                    FROM tb_notificaciones_expo n
                    LEFT JOIN tb_usuarios u ON u.cod_usuario = n.cod_usuario_admin
                    WHERE n.cod_empresa = :cod_empresa
                    ORDER BY n.fecha DESC
                    LIMIT 0," . (int)$limite;
        $resp = Conexion::buscarVariosRegistro($query, [':cod_empresa' => $this->cod_empresa]);
        return $resp ? $resp : [];
    }

    /* Cuántos clientes recibirían un envío masivo ahora mismo */
    public function totalClientesAlcanzables()
    {
        $query = "SELECT COUNT(DISTINCT t.cod_usuario) as total
                    FROM tb_push_tokens t
                    INNER JOIN tb_usuarios u ON u.cod_usuario = t.cod_usuario
                    WHERE u.cod_empresa = :cod_empresa
                    AND u.cod_rol = 4
                    AND u.estado = 'A'";
        $resp = Conexion::buscarRegistro($query, [':cod_empresa' => $this->cod_empresa]);
        return $resp ? (int)$resp['total'] : 0;
    }

    /* Punto único de entrada para envíos masivos a clientes */
    public function enviar($tipo, $titulo, $mensaje, $data = [])
    {
        $tokens = $this->obtenerTokensClientesEmpresa();
        if (empty($tokens)) {
            return ['success' => 0, 'mensaje' => 'No hay clientes con notificaciones push registradas', 'total_enviados' => 0];
        }

        $data['type'] = $tipo;
        $mensajes = [];
        foreach ($tokens as $token) {
            $mensajes[] = ["to" => $token, "title" => $titulo, "body" => $mensaje, "data" => $data];
        }
        $this->enviarExpoPush($mensajes);

        $totalEnviados = count($tokens);
        $this->registrarHistorial($tipo, $titulo, $mensaje, $data, $totalEnviados);

        return ['success' => 1, 'mensaje' => 'Notificación enviada', 'total_enviados' => $totalEnviados];
    }

    /* Mensaje libre a UN usuario (cliente o motorizado). El título es el nombre de la empresa,
       igual que la campana de flota.php (api_flotas notificar-motorizado). */
    public function enviarAUsuario($cod_usuario, $mensaje)
    {
        $usuario = $this->obtenerUsuarioPropio($cod_usuario);
        if (!$usuario) {
            return ['success' => 0, 'mensaje' => 'Este usuario no pertenece a tu empresa'];
        }

        $tokens = Conexion::buscarVariosRegistro(
            "SELECT token, sonido FROM tb_push_tokens WHERE cod_usuario = :cod_usuario",
            [':cod_usuario' => $cod_usuario]
        );
        if (!$tokens) {
            return ['success' => 0, 'mensaje' => 'Este usuario no tiene notificaciones activas (debe iniciar sesión en la app)'];
        }

        $empresa = Conexion::buscarRegistro("SELECT nombre FROM tb_empresas WHERE cod_empresa = :cod_empresa", [':cod_empresa' => $this->cod_empresa]);
        $titulo = html_entity_decode($empresa['nombre']);

        $esMotorizado = $usuario['es_motorizado'] == 1;
        $data = ['type' => $esMotorizado ? 'mensaje_flota' : 'mensaje'];

        $mensajes = [];
        foreach ($tokens as $t) {
            $msg = ["to" => $t['token'], "title" => $titulo, "body" => $mensaje, "sound" => "default", "data" => $data];
            // La app de motorizados crea un canal Android por sonido (pedidos_<sonido>), mismo criterio que api_flotas
            if ($esMotorizado) {
                $msg['channelId'] = 'pedidos_' . ($t['sonido'] ?: 'alarm_clock');
            }
            $mensajes[] = $msg;
        }
        $this->enviarExpoPush($mensajes);

        $this->registrarHistorial('mensaje', $titulo, $mensaje, ['cod_usuario' => $cod_usuario, 'destinatario' => $usuario['nombre']], 1);

        return ['success' => 1, 'mensaje' => 'Notificación enviada'];
    }

    /* El usuario debe ser de la empresa o un motorizado activo de su flota */
    private function obtenerUsuarioPropio($cod_usuario)
    {
        $query = "SELECT u.cod_usuario, CONCAT(u.nombre, ' ', u.apellido) as nombre,
                        IF(me.cod_motorizado_empresa IS NOT NULL OR u.cod_rol IN (" . implode(',', $this->ROLES_MOTORIZADO) . "), 1, 0) as es_motorizado
                    FROM tb_usuarios u
                    LEFT JOIN tb_motorizado_empresa me ON me.cod_usuario = u.cod_usuario AND me.cod_empresa = :cod_empresa1 AND me.estado = 'A'
                    WHERE u.cod_usuario = :cod_usuario
                    AND (u.cod_empresa = :cod_empresa2 OR me.cod_motorizado_empresa IS NOT NULL)";
        return Conexion::buscarRegistro($query, [
            ':cod_usuario' => $cod_usuario,
            ':cod_empresa1' => $this->cod_empresa,
            ':cod_empresa2' => $this->cod_empresa,
        ]);
    }

    /* Tokens de los clientes activos de la empresa (broadcast). Solo rol cliente: tb_push_tokens
       también guarda tokens de motorizados y cajeros. */
    private function obtenerTokensClientesEmpresa()
    {
        $query = "SELECT t.token
                    FROM tb_push_tokens t
                    INNER JOIN tb_usuarios u ON u.cod_usuario = t.cod_usuario
                    WHERE u.cod_empresa = :cod_empresa
                    AND u.cod_rol = 4
                    AND u.estado = 'A'";
        $registros = Conexion::buscarVariosRegistro($query, [':cod_empresa' => $this->cod_empresa]);
        if (!$registros) return [];
        return array_column($registros, 'token');
    }

    /* Envío crudo a la API de Expo, en chunks de 100 (límite de Expo por request).
       Los tokens que Expo reporta como DeviceNotRegistered se borran de tb_push_tokens. */
    private function enviarExpoPush($mensajes)
    {
        foreach (array_chunk($mensajes, 100) as $chunk) {
            $ch = curl_init("https://exp.host/--/api/v2/push/send");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Accept: application/json",
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($chunk));
            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if (!isset($response['data']) || !is_array($response['data'])) continue;
            foreach ($response['data'] as $i => $ticket) {
                $error = isset($ticket['details']['error']) ? $ticket['details']['error'] : '';
                if ($error === 'DeviceNotRegistered' && isset($chunk[$i])) {
                    Conexion::ejecutar("DELETE FROM tb_push_tokens WHERE token = :token", [':token' => $chunk[$i]['to']]);
                }
            }
        }
    }

    private function registrarHistorial($tipo, $titulo, $mensaje, $data, $totalEnviados)
    {
        Conexion::ejecutar("SET NAMES 'utf8mb4'", NULL);
        $query = "INSERT INTO tb_notificaciones_expo(cod_empresa, tipo, titulo, mensaje, data, cod_usuario_admin, total_enviados, fecha)
                    VALUES (:cod_empresa, :tipo, :titulo, :mensaje, :data, :cod_usuario_admin, :total_enviados, NOW())";
        return Conexion::ejecutar($query, [
            ':cod_empresa' => $this->cod_empresa,
            ':tipo' => $tipo,
            ':titulo' => $titulo,
            ':mensaje' => $mensaje,
            ':data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ':cod_usuario_admin' => $this->session['cod_usuario'],
            ':total_enviados' => $totalEnviados,
        ]);
    }
}
?>
