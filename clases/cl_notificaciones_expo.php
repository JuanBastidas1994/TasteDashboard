<?php
/* ==========================================================================
   NOTIFICACIONES PUSH (Expo)
   - Campañas a clientes desde notificaciones.php: inmediatas o programadas, con audiencia.
   - Mensaje libre a UN usuario (campana en clientes.php, cliente_detalle.php, usuario_detalle.php).
   - Automáticas (calificación, cumpleaños, recompra) que dispara cron/notificaciones_push.php.
   Todo envío queda en tb_notificaciones_expo y su id viaja en data.notificacion_id: la app lo
   reporta al tocar la notificación (api: POST notificaciones/abierta) para medir aperturas.
   Lee tb_push_tokens (la llenan las apps vía api/api_flotas), misma base de datos (jc_taste).
   ========================================================================== */

class cl_notificaciones_expo
{
    public $session;
    public $cod_empresa;

    /* Roles que usan la app de motorizados (Delivery y Motorizado) */
    private $ROLES_MOTORIZADO = [17, 21];

    /* Sin $cod_empresa toma la empresa de la sesión (dashboard); el cron la pasa explícita */
    public function __construct($cod_empresa = null)
    {
        date_default_timezone_set('America/Guayaquil');
        if ($cod_empresa === null) {
            $this->session = getSession();
            $this->cod_empresa = $this->session['cod_empresa'];
        } else {
            $this->session = null;
            $this->cod_empresa = (int)$cod_empresa;
        }
    }

    /* ---------------------------------------------------------------------
       CONSULTAS PARA EL DASHBOARD
       --------------------------------------------------------------------- */

    /* Tipos que envía el cron sin intervención del admin */
    public $TIPOS_AUTOMATICOS = ['calificacion', 'cumpleanos', 'cumpleanos_previo', 'recompra'];

    /* Historial con nombre del admin y aperturas. Las programadas pendientes primero.
       $filtro: 'todas' | 'programadas' (pendientes y las que ya salieron) | 'automaticas' */
    public function lista($limite = 100, $filtro = 'todas')
    {
        $automaticos = "'" . implode("','", $this->TIPOS_AUTOMATICOS) . "'";
        $where = '';
        if ($filtro === 'programadas') $where = " AND n.fecha_programada IS NOT NULL";
        if ($filtro === 'automaticas') $where = " AND n.tipo IN ($automaticos)";

        $query = "SELECT n.*, CONCAT(u.nombre, ' ', u.apellido) as admin,
                        (SELECT COUNT(*) FROM tb_notificaciones_expo_aperturas a WHERE a.notificacion_id = n.id) as aperturas
                    FROM tb_notificaciones_expo n
                    LEFT JOIN tb_usuarios u ON u.cod_usuario = n.cod_usuario_admin
                    WHERE n.cod_empresa = :cod_empresa $where
                    ORDER BY (n.estado = 'PROGRAMADA') DESC, COALESCE(n.fecha_programada, n.fecha) DESC
                    LIMIT 0," . (int)$limite;
        $resp = Conexion::buscarVariosRegistro($query, [':cod_empresa' => $this->cod_empresa]);
        return $resp ? $resp : [];
    }

    public function totalProgramadasPendientes()
    {
        $resp = Conexion::buscarRegistro(
            "SELECT COUNT(*) as total FROM tb_notificaciones_expo WHERE cod_empresa = :cod_empresa AND estado = 'PROGRAMADA'",
            [':cod_empresa' => $this->cod_empresa]
        );
        return $resp ? (int)$resp['total'] : 0;
    }

    /* Tarjetas de "Resumen rápido" del mes en curso */
    public function resumenMes()
    {
        $inicio = date('Y-m-01 00:00:00');
        $envios = Conexion::buscarRegistro(
            "SELECT COUNT(*) as enviadas, COALESCE(SUM(total_ok), 0) as entregadas
                FROM tb_notificaciones_expo
                WHERE cod_empresa = :cod_empresa AND estado = 'ENVIADA' AND fecha_envio >= :inicio",
            [':cod_empresa' => $this->cod_empresa, ':inicio' => $inicio]
        );
        $aperturas = Conexion::buscarRegistro(
            "SELECT COUNT(*) as abiertas
                FROM tb_notificaciones_expo_aperturas a
                INNER JOIN tb_notificaciones_expo n ON n.id = a.notificacion_id
                WHERE n.cod_empresa = :cod_empresa AND n.fecha_envio >= :inicio",
            [':cod_empresa' => $this->cod_empresa, ':inicio' => $inicio]
        );

        $entregadas = (int)$envios['entregadas'];
        $abiertas = (int)$aperturas['abiertas'];
        return [
            'enviadas'   => (int)$envios['enviadas'],
            'entregadas' => $entregadas,
            'abiertas'   => $abiertas,
            'tasa'       => $entregadas > 0 ? round($abiertas * 100 / $entregadas, 1) : 0,
        ];
    }

    /* ---------------------------------------------------------------------
       AUDIENCIA
       Formato guardado en tb_notificaciones_expo.audiencia (JSON):
         {"tipo":"todos"} | {"tipo":"inactivos","dias":30} | {"tipo":"sucursal","cod_sucursal":5}
       --------------------------------------------------------------------- */

    public function normalizarAudiencia($tipo, $valor = null)
    {
        if ($tipo === 'inactivos') {
            $dias = (int)$valor;
            if ($dias < 1) return null;
            return ['tipo' => 'inactivos', 'dias' => $dias];
        }
        if ($tipo === 'sucursal') {
            $sucursal = Conexion::buscarRegistro(
                "SELECT cod_sucursal FROM tb_sucursales WHERE cod_sucursal = :cod_sucursal AND cod_empresa = :cod_empresa",
                [':cod_sucursal' => (int)$valor, ':cod_empresa' => $this->cod_empresa]
            );
            if (!$sucursal) return null;
            return ['tipo' => 'sucursal', 'cod_sucursal' => (int)$valor];
        }
        return ['tipo' => 'todos'];
    }

    public function describirAudiencia($audiencia)
    {
        if (is_string($audiencia)) $audiencia = json_decode($audiencia, true);
        if (!$audiencia) return 'Todos los clientes';

        switch ($audiencia['tipo']) {
            case 'inactivos':
                return 'Sin pedir hace ' . $audiencia['dias'] . ' días';
            case 'sucursal':
                $sucursal = Conexion::buscarRegistro("SELECT nombre FROM tb_sucursales WHERE cod_sucursal = :cod_sucursal", [':cod_sucursal' => $audiencia['cod_sucursal']]);
                return 'Clientes de ' . ($sucursal ? html_entity_decode($sucursal['nombre']) : 'una sucursal');
            case 'usuario':
                return isset($audiencia['nombre']) ? $audiencia['nombre'] : 'Un usuario';
            case 'automatica':
                return 'Automática';
            default:
                return 'Todos los clientes';
        }
    }

    /* Clientes (rol 4) activos de la empresa con token, filtrados por la audiencia */
    private function sqlClientesAudiencia($audiencia, &$params)
    {
        $params = [':cod_empresa' => $this->cod_empresa];
        $sql = "FROM tb_push_tokens t
                INNER JOIN tb_usuarios u ON u.cod_usuario = t.cod_usuario
                WHERE u.cod_empresa = :cod_empresa
                AND u.cod_rol = 4
                AND u.estado = 'A'";

        if ($audiencia['tipo'] === 'inactivos') {
            $sql .= " AND u.cod_usuario IN (
                        SELECT cod_usuario FROM tb_orden_cabecera
                        WHERE cod_empresa = :cod_empresa_ord AND estado = 'ENTREGADA'
                        GROUP BY cod_usuario
                        HAVING MAX(fecha) < :limite)";
            $params[':cod_empresa_ord'] = $this->cod_empresa;
            $params[':limite'] = date('Y-m-d H:i:s', strtotime('-' . (int)$audiencia['dias'] . ' days'));
        } elseif ($audiencia['tipo'] === 'sucursal') {
            $sql .= " AND u.cod_usuario IN (
                        SELECT cod_usuario FROM tb_orden_cabecera
                        WHERE cod_empresa = :cod_empresa_ord AND cod_sucursal = :cod_sucursal AND estado = 'ENTREGADA')";
            $params[':cod_empresa_ord'] = $this->cod_empresa;
            $params[':cod_sucursal'] = (int)$audiencia['cod_sucursal'];
        }
        return $sql;
    }

    public function contarAudiencia($audiencia)
    {
        $params = [];
        $sql = "SELECT COUNT(DISTINCT u.cod_usuario) as total " . $this->sqlClientesAudiencia($audiencia, $params);
        $resp = Conexion::buscarRegistro($sql, $params);
        return $resp ? (int)$resp['total'] : 0;
    }

    private function tokensAudiencia($audiencia)
    {
        $params = [];
        $sql = "SELECT DISTINCT t.token " . $this->sqlClientesAudiencia($audiencia, $params);
        $registros = Conexion::buscarVariosRegistro($sql, $params);
        return $registros ? array_column($registros, 'token') : [];
    }

    /* ---------------------------------------------------------------------
       CAMPAÑAS (notificaciones.php)
       --------------------------------------------------------------------- */

    /* $fechaProgramada 'Y-m-d H:i:s' o null para enviar ya */
    public function crearCampana($tipo, $titulo, $mensaje, $data, $audiencia, $fechaProgramada = null)
    {
        if ($fechaProgramada !== null && strtotime($fechaProgramada) <= time()) {
            return ['success' => 0, 'mensaje' => 'La fecha programada debe ser en el futuro'];
        }

        $id = $this->insertarRegistro([
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'data' => $data,
            'audiencia' => $audiencia,
            'estado' => $fechaProgramada ? 'PROGRAMADA' : 'ENVIANDO',
            'fecha_programada' => $fechaProgramada,
        ]);
        if (!$id) {
            return ['success' => 0, 'mensaje' => 'No se pudo registrar la notificación'];
        }

        if ($fechaProgramada) {
            return ['success' => 1, 'mensaje' => 'Notificación programada para el ' . date('d/m/Y H:i', strtotime($fechaProgramada))];
        }
        return $this->enviarCampana($id, 'ENVIANDO');
    }

    /* Envía una campaña ya registrada. $estadoActual protege de doble envío (cron vs. dashboard). */
    public function enviarCampana($id, $estadoActual = 'PROGRAMADA')
    {
        $tomada = $this->ejecutarFilas(
            "UPDATE tb_notificaciones_expo SET estado = 'ENVIANDO' WHERE id = :id AND cod_empresa = :cod_empresa AND estado = :estado",
            [':id' => $id, ':cod_empresa' => $this->cod_empresa, ':estado' => $estadoActual]
        );
        if (!$tomada && $estadoActual !== 'ENVIANDO') {
            return ['success' => 0, 'mensaje' => 'La notificación ya fue procesada'];
        }

        $registro = Conexion::buscarRegistro("SELECT * FROM tb_notificaciones_expo WHERE id = :id", [':id' => $id]);
        $audiencia = json_decode($registro['audiencia'], true) ?: ['tipo' => 'todos'];
        $tokens = $this->tokensAudiencia($audiencia);

        if (empty($tokens)) {
            if ($registro['fecha_programada']) {
                $this->actualizarResultado($id, 0, 0, 'ERROR');
            } else {
                Conexion::ejecutar("DELETE FROM tb_notificaciones_expo WHERE id = :id", [':id' => $id]);
            }
            return ['success' => 0, 'mensaje' => 'No hay clientes con notificaciones activas para esa audiencia', 'total_enviados' => 0];
        }

        $data = json_decode($registro['data'], true) ?: [];
        $data['type'] = $registro['tipo'];
        $data['notificacion_id'] = (int)$id;

        $mensajes = [];
        foreach ($tokens as $token) {
            $mensajes[] = ["to" => $token, "title" => $registro['titulo'], "body" => $registro['mensaje'], "sound" => "default", "data" => $data];
        }
        $resultado = $this->enviarExpoPush($mensajes);
        $this->actualizarResultado($id, $resultado['ok'], $resultado['error'], $resultado['ok'] > 0 ? 'ENVIADA' : 'ERROR');

        return ['success' => 1, 'mensaje' => 'Notificación enviada', 'total_enviados' => $resultado['ok']];
    }

    public function cancelarProgramada($id)
    {
        $ok = $this->ejecutarFilas(
            "UPDATE tb_notificaciones_expo SET estado = 'CANCELADA' WHERE id = :id AND cod_empresa = :cod_empresa AND estado = 'PROGRAMADA'",
            [':id' => $id, ':cod_empresa' => $this->cod_empresa]
        );
        return $ok
            ? ['success' => 1, 'mensaje' => 'Notificación cancelada']
            : ['success' => 0, 'mensaje' => 'La notificación ya no está programada'];
    }

    /* Cron: envía todas las programadas vencidas de todas las empresas */
    public static function procesarProgramadas()
    {
        date_default_timezone_set('America/Guayaquil');
        $pendientes = Conexion::buscarVariosRegistro(
            "SELECT id, cod_empresa FROM tb_notificaciones_expo WHERE estado = 'PROGRAMADA' AND fecha_programada <= :ahora ORDER BY fecha_programada",
            [':ahora' => date('Y-m-d H:i:s')]
        );
        $resultados = [];
        foreach ($pendientes ?: [] as $p) {
            $cl = new cl_notificaciones_expo($p['cod_empresa']);
            $resultados[$p['id']] = $cl->enviarCampana($p['id']);
        }
        return $resultados;
    }

    /* ---------------------------------------------------------------------
       MENSAJE DIRECTO A UN USUARIO (clientes / motorizados)
       --------------------------------------------------------------------- */

    /* El título es el nombre de la empresa, igual que la campana de flota.php (api_flotas) */
    public function enviarAUsuario($cod_usuario, $mensaje)
    {
        $usuario = $this->obtenerUsuarioPropio($cod_usuario);
        if (!$usuario) {
            return ['success' => 0, 'mensaje' => 'Este usuario no pertenece a tu empresa'];
        }

        $tokens = $this->tokensUsuario($cod_usuario);
        if (!$tokens) {
            return ['success' => 0, 'mensaje' => 'Este usuario no tiene notificaciones activas (debe iniciar sesión en la app)'];
        }

        $titulo = $this->nombreEmpresa();
        $esMotorizado = $usuario['es_motorizado'] == 1;

        $id = $this->insertarRegistro([
            'tipo' => 'mensaje',
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'data' => ['cod_usuario' => (int)$cod_usuario, 'destinatario' => $usuario['nombre']],
            'audiencia' => ['tipo' => 'usuario', 'nombre' => $usuario['nombre']],
            'estado' => 'ENVIANDO',
        ]);
        $data = ['type' => $esMotorizado ? 'mensaje_flota' : 'mensaje', 'notificacion_id' => (int)$id];

        $mensajes = [];
        foreach ($tokens as $t) {
            $msg = ["to" => $t['token'], "title" => $titulo, "body" => $mensaje, "sound" => "default", "data" => $data];
            // La app de motorizados crea un canal Android por sonido (pedidos_<sonido>), mismo criterio que api_flotas
            if ($esMotorizado) {
                $msg['channelId'] = 'pedidos_' . ($t['sonido'] ?: 'alarm_clock');
            }
            $mensajes[] = $msg;
        }
        $resultado = $this->enviarExpoPush($mensajes);
        $this->actualizarResultado($id, $resultado['ok'], $resultado['error'], $resultado['ok'] > 0 ? 'ENVIADA' : 'ERROR');

        if ($resultado['ok'] == 0) {
            return ['success' => 0, 'mensaje' => 'Expo rechazó el envío, el usuario debe volver a iniciar sesión en la app'];
        }
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

    /* ---------------------------------------------------------------------
       AUTOMÁTICAS (cron/notificaciones_push.php)
       Se agrupan en UN registro por empresa, tipo y día para que el historial no se llene
       de filas de 1 destinatario; las aperturas se miden sobre ese registro.
       --------------------------------------------------------------------- */

    /* $envios: [['tokens' => [...], 'titulo' => '', 'mensaje' => '', 'data' => []], ...] (uno por usuario) */
    public function enviarAutomatica($tipo, $tituloHistorial, $mensajeHistorial, $envios)
    {
        if (empty($envios)) return ['ok' => 0, 'error' => 0];

        $id = $this->registroDiario($tipo, $tituloHistorial, $mensajeHistorial);

        $mensajes = [];
        foreach ($envios as $envio) {
            $data = isset($envio['data']) ? $envio['data'] : [];
            if (!isset($data['type'])) $data['type'] = $tipo;
            $data['notificacion_id'] = (int)$id;
            foreach ($envio['tokens'] as $token) {
                $mensajes[] = ["to" => $token, "title" => $envio['titulo'], "body" => $envio['mensaje'], "sound" => "default", "data" => $data];
            }
        }
        $resultado = $this->enviarExpoPush($mensajes);

        Conexion::ejecutar(
            "UPDATE tb_notificaciones_expo
                SET total_enviados = total_enviados + :enviados, total_ok = total_ok + :ok, total_error = total_error + :error, fecha_envio = :ahora
                WHERE id = :id",
            [':enviados' => $resultado['ok'], ':ok' => $resultado['ok'], ':error' => $resultado['error'], ':ahora' => date('Y-m-d H:i:s'), ':id' => $id]
        );
        return $resultado;
    }

    private function registroDiario($tipo, $titulo, $mensaje)
    {
        $existente = Conexion::buscarRegistro(
            "SELECT id FROM tb_notificaciones_expo
                WHERE cod_empresa = :cod_empresa AND tipo = :tipo AND estado = 'ENVIADA'
                AND fecha >= :hoy AND fecha < :manana",
            [':cod_empresa' => $this->cod_empresa, ':tipo' => $tipo, ':hoy' => date('Y-m-d 00:00:00'), ':manana' => date('Y-m-d 00:00:00', strtotime('+1 day'))]
        );
        if ($existente) return $existente['id'];

        return $this->insertarRegistro([
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'data' => [],
            'audiencia' => ['tipo' => 'automatica'],
            'estado' => 'ENVIADA',
        ]);
    }

    /* true si se registró ahora (no se había enviado antes con esa clave) */
    public function marcarAutomatica($cod_usuario, $tipo, $clave)
    {
        return $this->ejecutarFilas(
            "INSERT IGNORE INTO tb_notificaciones_auto_log (cod_empresa, cod_usuario, tipo, clave, fecha)
                VALUES (:cod_empresa, :cod_usuario, :tipo, :clave, :fecha)",
            [':cod_empresa' => $this->cod_empresa, ':cod_usuario' => $cod_usuario, ':tipo' => $tipo, ':clave' => $clave, ':fecha' => date('Y-m-d H:i:s')]
        ) > 0;
    }

    public function configAutomaticas()
    {
        $config = Conexion::buscarRegistro("SELECT * FROM tb_notificaciones_auto_config WHERE cod_empresa = :cod_empresa", [':cod_empresa' => $this->cod_empresa]);
        if ($config) return $config;
        return [
            'cod_empresa' => $this->cod_empresa,
            'recompra_activo' => 0,
            'recompra_dias' => 15,
            'recompra_titulo' => '¡Te extrañamos, {nombre}! 😋',
            'recompra_mensaje' => 'Hace días que no pides. Antójate de algo rico y haz tu pedido desde la app.',
        ];
    }

    public function guardarConfigRecompra($activo, $dias, $titulo, $mensaje)
    {
        return Conexion::ejecutar(
            "INSERT INTO tb_notificaciones_auto_config (cod_empresa, recompra_activo, recompra_dias, recompra_titulo, recompra_mensaje, fecha_update)
                VALUES (:cod_empresa, :activo, :dias, :titulo, :mensaje, :fecha)
                ON DUPLICATE KEY UPDATE recompra_activo = VALUES(recompra_activo), recompra_dias = VALUES(recompra_dias),
                    recompra_titulo = VALUES(recompra_titulo), recompra_mensaje = VALUES(recompra_mensaje), fecha_update = VALUES(fecha_update)",
            [':cod_empresa' => $this->cod_empresa, ':activo' => $activo ? 1 : 0, ':dias' => $dias, ':titulo' => $titulo, ':mensaje' => $mensaje, ':fecha' => date('Y-m-d H:i:s')]
        );
    }

    /* ---------------------------------------------------------------------
       COMUNES
       --------------------------------------------------------------------- */

    public function tokensUsuario($cod_usuario)
    {
        $tokens = Conexion::buscarVariosRegistro(
            "SELECT token, sonido FROM tb_push_tokens WHERE cod_usuario = :cod_usuario",
            [':cod_usuario' => $cod_usuario]
        );
        return $tokens ? $tokens : [];
    }

    public function nombreEmpresa()
    {
        $empresa = Conexion::buscarRegistro("SELECT nombre FROM tb_empresas WHERE cod_empresa = :cod_empresa", [':cod_empresa' => $this->cod_empresa]);
        return $empresa ? html_entity_decode($empresa['nombre']) : '';
    }

    private function insertarRegistro($r)
    {
        $ok = Conexion::ejecutar(
            "INSERT INTO tb_notificaciones_expo(cod_empresa, tipo, titulo, mensaje, data, cod_usuario_admin, total_enviados, estado, audiencia, fecha_programada, fecha)
                VALUES (:cod_empresa, :tipo, :titulo, :mensaje, :data, :cod_usuario_admin, 0, :estado, :audiencia, :fecha_programada, :fecha)",
            [
                ':cod_empresa' => $this->cod_empresa,
                ':tipo' => $r['tipo'],
                ':titulo' => $r['titulo'],
                ':mensaje' => $r['mensaje'],
                ':data' => json_encode($r['data'], JSON_UNESCAPED_UNICODE),
                ':cod_usuario_admin' => $this->session ? $this->session['cod_usuario'] : 0,
                ':estado' => $r['estado'],
                ':audiencia' => json_encode($r['audiencia'], JSON_UNESCAPED_UNICODE),
                ':fecha_programada' => isset($r['fecha_programada']) ? $r['fecha_programada'] : null,
                ':fecha' => date('Y-m-d H:i:s'),
            ]
        );
        return $ok ? (int)Conexion::obtenerConexion()->lastInsertId() : 0;
    }

    private function actualizarResultado($id, $ok, $error, $estado)
    {
        return Conexion::ejecutar(
            "UPDATE tb_notificaciones_expo SET total_enviados = :enviados, total_ok = :ok, total_error = :error, estado = :estado, fecha_envio = :ahora WHERE id = :id",
            [':enviados' => $ok, ':ok' => $ok, ':error' => $error, ':estado' => $estado, ':ahora' => date('Y-m-d H:i:s'), ':id' => $id]
        );
    }

    /* UPDATE/INSERT que devuelve filas afectadas (Conexion::ejecutar solo devuelve bool) */
    private function ejecutarFilas($sql, $params)
    {
        try {
            $rs = Conexion::obtenerConexion()->prepare($sql);
            $rs->execute($params);
            return $rs->rowCount();
        } catch (Exception $ex) {
            error_log('cl_notificaciones_expo: ' . $ex->getMessage());
            return 0;
        }
    }

    /* Envío crudo a la API de Expo, en chunks de 100 (límite de Expo por request).
       Devuelve cuántos aceptó Expo. Los tokens DeviceNotRegistered se borran de tb_push_tokens. */
    private function enviarExpoPush($mensajes)
    {
        $ok = 0;
        $error = 0;
        foreach (array_chunk($mensajes, 100) as $chunk) {
            $ch = curl_init("https://exp.host/--/api/v2/push/send");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Accept: application/json",
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($chunk));
            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if (!isset($response['data']) || !is_array($response['data'])) {
                $error += count($chunk);
                continue;
            }
            foreach ($response['data'] as $i => $ticket) {
                if (isset($ticket['status']) && $ticket['status'] === 'ok') {
                    $ok++;
                    continue;
                }
                $error++;
                $tipoError = isset($ticket['details']['error']) ? $ticket['details']['error'] : '';
                if ($tipoError === 'DeviceNotRegistered' && isset($chunk[$i])) {
                    Conexion::ejecutar("DELETE FROM tb_push_tokens WHERE token = :token", [':token' => $chunk[$i]['to']]);
                }
            }
        }
        return ['ok' => $ok, 'error' => $error];
    }
}
?>
